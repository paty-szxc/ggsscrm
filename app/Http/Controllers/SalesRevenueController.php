<?php

namespace App\Http\Controllers;

use App\Imports\SalesRevenueImport;
use App\Models\SalesRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SalesRevenueController extends Controller
{
    // Helper to sanitize currency input
    private function parseCurrency($value) {
        if (is_null($value) || $value === '') return null;
        return floatval(str_replace(',', '', $value));
    }

    // Helper to get withholding tax amount for calculation
    private function getWithholdingTaxAmount($withholdingTax, $projectCost) {
        if (is_null($withholdingTax) || $withholdingTax === '') return 0;
        if (is_numeric($withholdingTax)) return floatval($withholdingTax);
        // Check for pattern like 'w/ 2% TAX'
        if (preg_match('/([\d.]+)\s*%/', $withholdingTax, $matches)) {
            $percent = floatval($matches[1]);
            return $projectCost * ($percent / 100);
        }
        return 0;
    }

    public function index(){
        $sales_revenue = SalesRevenue::orderBy('date_of_survey', 'desc')->get();

        $sales_revenue->transform(function($item){
            // Parse project cost
            $projectCost = floatval(str_replace(',', '', $item->project_cost));
            // Calculate withholding tax amount
            $withholdingTaxAmount = 0;
            if (preg_match('/([\d.]+)\s*%/', $item->withholding_tax, $matches)) {
                $percent = floatval($matches[1]);
                $withholdingTaxAmount = $projectCost * ($percent / 100);
            } elseif (is_numeric($item->withholding_tax)) {
                $withholdingTaxAmount = floatval($item->withholding_tax);
            }
            // Calculate total collections
            $total = 0;
            foreach (['first_collection', 'second_collection', 'third_collection', 'fourth_collection'] as $field) {
                $total += floatval(str_replace(',', '', $item->$field));
            }
            // Calculate receivable balance
            $receivableBal = $projectCost - ($total + $withholdingTaxAmount);
            if ($receivableBal < 0) $receivableBal = 0;
            $item->receivable_bal = number_format($receivableBal, 2);

            // Set fully paid date if receivable is zero or less
            if ($receivableBal <= 0) {
                $dates = array_filter([
                    $item->first_date_of_collection,
                    $item->second_date_of_collection,
                    $item->third_date_of_collection,
                    $item->fourth_date_of_collection
                ]);
                $item->fully_paid_date = !empty($dates) ? max($dates) : null;
            }

            $item->project_cost = number_format($projectCost, 2);
            $item->first_collection = $item->first_collection !== null ? number_format(floatval(str_replace(',', '', $item->first_collection)), 2) : '';
            $item->second_collection =  $item->second_collection !== null ? number_format(floatval(str_replace(',', '', $item->second_collection)), 2) : '';
            $item->third_collection =  $item->third_collection !== null ? number_format(floatval(str_replace(',', '', $item->third_collection)), 2) : '';
            $item->fourth_collection =  $item->fourth_collection !== null ? number_format(floatval(str_replace(',', '', $item->fourth_collection)), 2) : '';
            $item->total =  $item->total !== null ? number_format(floatval(str_replace(',', '', $item->total)), 2) : '';
            return $item;
        });
        return $sales_revenue;
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|  max:2048',
        ]);
        try{
            Excel::import(new SalesRevenueImport(), $request->file('file'));
            if($request->wantsJson()){
                return response()->json([
                    'success' => true,
                    'message' => 'Sales revenue imported successfully!'
                ]);
            }
            return redirect()->back()->with('success', 'Sales revenue imported successfully!');
        }
        catch(\Exception $e){
            Log::error('Import error: ' . $e->getMessage());
            if($request->wantsJson()){
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import sales revenue',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function insert(Request $req){
        $formatDate = function($date) {
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };

        $projectCost = $this->parseCurrency($req->to_update['project_cost'] ?? null);
        $withholdingTaxStr = $req->to_update['withholding_tax'] ?? null;
        $withholdingTaxAmount = $this->getWithholdingTaxAmount($withholdingTaxStr, $projectCost);
        $totalCollections =
            $this->parseCurrency($req->to_update['first_collection'] ?? null) +
            $this->parseCurrency($req->to_update['second_collection'] ?? null) +
            $this->parseCurrency($req->to_update['third_collection'] ?? null) +
            $this->parseCurrency($req->to_update['fourth_collection'] ?? null);
        $receivableBal = $projectCost - ($totalCollections + $withholdingTaxAmount);
        if ($receivableBal < 0) $receivableBal = 0;

        $insert = SalesRevenue::create([
            'date_of_survey' => $formatDate($req->to_update['date_of_survey'] ?? null), 
            'location' => $req->to_update['location'] ?? null, 
            'type_of_survey' => $req->to_update['type_of_survey'] ?? null, 
            'receipt_no' => $req->to_update['receipt_no'] ?? null, 
            'project_cost' => $projectCost, 
            'first_date_of_collection' => $formatDate($req->to_update['first_date_of_collection'] ?? null), 
            'first_collection' => $this->parseCurrency($req->to_update['first_collection'] ?? null), 
            'second_date_of_collection' => $formatDate($req->to_update['second_date_of_collection'] ?? null), 
            'second_collection' => $this->parseCurrency($req->to_update['second_collection'] ?? null), 
            'third_date_of_collection' => $formatDate($req->to_update['third_date_of_collection'] ?? null), 
            'third_collection' => $this->parseCurrency($req->to_update['third_collection'] ?? null), 
            'fourth_date_of_collection' => $formatDate($req->to_update['fourth_date_of_collection'] ?? null), 
            'fourth_collection' => $this->parseCurrency($req->to_update['fourth_collection'] ?? null),
            'total' => $this->parseCurrency($req->to_update['total'] ?? null),
            'withholding_tax' => $withholdingTaxStr, //store as string
            'receivable_bal' => $receivableBal, 
            'remarks' => $req->to_update['remarks'] ?? null, 
            'fully_paid_date' => $formatDate($req->to_update['fully_paid_date'] ?? null), 
        ]);

        return response()->json([
            'message' => 'Sales revenue data inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        $formatDate = function($date) {
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };

        $projectCost = $this->parseCurrency($req->to_update['project_cost'] ?? null);
        $withholdingTaxStr = $req->to_update['withholding_tax'] ?? null;
        $withholdingTaxAmount = $this->getWithholdingTaxAmount($withholdingTaxStr, $projectCost);
        $totalCollections =
            $this->parseCurrency($req->to_update['first_collection'] ?? null) +
            $this->parseCurrency($req->to_update['second_collection'] ?? null) +
            $this->parseCurrency($req->to_update['third_collection'] ?? null) +
            $this->parseCurrency($req->to_update['fourth_collection'] ?? null);
        $receivableBal = $projectCost - ($totalCollections + $withholdingTaxAmount);
        if ($receivableBal < 0) $receivableBal = 0;

        $record = SalesRevenue::find($req->to_update['id']);

        $record->update([
            'date_of_survey' => $formatDate($req->to_update['date_of_survey'] ?? null), 
            'location' => $req->to_update['location'] ?? null, 
            'type_of_survey' => $req->to_update['type_of_survey'] ?? null, 
            'receipt_no' => $req->to_update['receipt_no'] ?? null, 
            'project_cost' => $projectCost, 
            'first_date_of_collection' => $formatDate($req->to_update['first_date_of_collection'] ?? null), 
            'first_collection' => $this->parseCurrency($req->to_update['first_collection'] ?? null), 
            'second_date_of_collection' => $formatDate($req->to_update['second_date_of_collection'] ?? null), 
            'second_collection' => $this->parseCurrency($req->to_update['second_collection'] ?? null), 
            'third_date_of_collection' => $formatDate($req->to_update['third_date_of_collection'] ?? null), 
            'third_collection' => $this->parseCurrency($req->to_update['third_collection'] ?? null), 
            'fourth_date_of_collection' => $formatDate($req->to_update['fourth_date_of_collection'] ?? null), 
            'fourth_collection' => $this->parseCurrency($req->to_update['fourth_collection'] ?? null),
            'total' => $this->parseCurrency($req->to_update['total'] ?? null),
            'withholding_tax' => $withholdingTaxStr, // store as string
            'receivable_bal' => $receivableBal, 
            'remarks' => $req->to_update['remarks'] ?? null, 
            'fully_paid_date' => $formatDate($req->to_update['fully_paid_date'] ?? null), 
        ]);

        return response()->json([
            'message' => 'Sales revenue data updated successfully!',
            'data' => $record,
        ], 200);
    }

    public function monthlyCosts(){
        $currentYear = date('Y');
        
        $results = DB::table('sales_revenue')
            ->select(
                DB::raw("DATE_FORMAT(date_of_survey, '%b') as month"),
                DB::raw('SUM(project_cost) as total_cost')
            )
            ->whereYear('date_of_survey', $currentYear)
            ->groupBy('month')
            ->orderByRaw("MONTH(STR_TO_DATE(CONCAT('$currentYear-', month, '-01'), '%Y-%b-%d'))")
            ->get();

        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $data = collect($allMonths)->map(function ($month) use ($results) {
            $found = $results->firstWhere('month', $month);
            return [
                'month' => $month,
                'total_cost' => $found ? $found->total_cost : 0
            ];
        });

        return response()->json($data);
    }

    public function yearlySales(){
        $currentYear = date('Y');
        
        Log::info('YearlySales called for year: ' . $currentYear);
        
        // Check if there are any sales records in the database
        $totalSalesRecords = DB::table('sales_revenue')->count();
        $salesWithDates = DB::table('sales_revenue')->whereNotNull('date_of_survey')->count();
        $salesThisYear = DB::table('sales_revenue')->whereYear('date_of_survey', $currentYear)->count();
        
        Log::info('Sales counts:', [
            'total' => $totalSalesRecords,
            'with_dates' => $salesWithDates,
            'this_year' => $salesThisYear
        ]);
        
        $totalSales = DB::table('sales_revenue')
            ->select(DB::raw('SUM(project_cost) as yearly_total'))
            ->whereRaw('YEAR(date_of_survey) = ?', [$currentYear])
            ->first();

        Log::info('YearlySales query result:', ['result' => $totalSales, 'currentYear' => $currentYear]);

        return response()->json([
            'year' => $currentYear,
            'total_sales' => $totalSales->yearly_total ?: 0
        ]);
    }
}
