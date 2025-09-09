<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ConstructionSalesRevenueImport;
use App\Models\ConstructionSalesRevenue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ConstructionSalesRevenueController extends Controller
{
    //helper to sanitize currency input
    private function parseCurrency($value) {
        if (is_null($value) || $value === '') return null;
        return floatval(str_replace(',', '', $value));
    }

    //helper to get withholding tax amount for calculation
    private function getWithholdingTaxAmount($withholdingTax, $projectCost){
        if(is_null($withholdingTax) || $withholdingTax === '') return 0;
        if(is_numeric($withholdingTax)) return floatval($withholdingTax);
        // Check for pattern like 'w/ 2% TAX'
        if(preg_match('/([\d.]+)\s*%/', $withholdingTax, $matches)){
            $percent = floatval($matches[1]);
            return $projectCost * ($percent / 100);
        }
        return 0;
    }

    public function index(){
        $sales_revenue = ConstructionSalesRevenue::orderBy('date', 'desc')->get();

        $sales_revenue->transform(function($item){
            //parse project cost
            $projectCost = floatval(str_replace(',', '', $item->project_cost));
            //calculate withholding tax amount
            $withholdingTaxAmount = 0;
            if(preg_match('/([\d.]+)\s*%/', $item->withholding_tax, $matches)){
                $percent = floatval($matches[1]);
                $withholdingTaxAmount = $projectCost * ($percent / 100);
            }
            elseif(is_numeric($item->withholding_tax)){
                $withholdingTaxAmount = floatval($item->withholding_tax);
            }
            //calculate total collections
            $total = 0;
            foreach (['first_collection', 'second_collection', 'third_collection', 'fourth_collection'] as $field) {
                $total += floatval(str_replace(',', '', $item->$field));
            }
            //calculate receivable balance
            $receivableBal = $projectCost - ($total + $withholdingTaxAmount);
            if ($receivableBal < 0) $receivableBal = 0;
            $item->receivable_bal = number_format($receivableBal, 2);
            //set fully paid date if receivable is zero or less
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
            $item->amount_gross_of_vat =  $item->amount_gross_of_vat !== null ? number_format(floatval(str_replace(',', '', $item->amount_gross_of_vat)), 2) : '';
            $item->net_of_vat =  $item->net_of_vat !== null ? number_format(floatval(str_replace(',', '', $item->net_of_vat)), 2) : '';
            $item->vat =  $item->vat !== null ? number_format(floatval(str_replace(',', '', $item->vat)), 2) : '';
            return $item;
        });
        return $sales_revenue;
    }

    public function import(Request $req){
        $req->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', //added max file size (10MB)
        ]);

        try{
            $file = $req->file('file');
            Excel::import(new ConstructionSalesRevenueImport, $file);
            return back()->with('success', 'Construction sales revenue imported successfully!');
        }
        catch(\Exception $e){
            $errorMessage = 'Import failed. ' . 
                ($e instanceof \Maatwebsite\Excel\Validators\ValidationException ?
                    'Please check your file format and data.' :
                    $e->getMessage());
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function insert(Request $req){
        $formatDate = function($date) {
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };

        $projectCost = $this->parseCurrency($req->to_update['project_cost'] ?? null);
        $withholdingTaxStr = $req->to_update['withholding_tax'] ?? null;
        $withholdingTaxAmount = $this->getWithholdingTaxAmount($withholdingTaxStr, $projectCost);
        $grossVat = $this->parseCurrency($req->to_update['amount_gross_of_vat'] ?? null);
        $netVat = $this->parseCurrency($req->to_update['net_of_vat'] ?? null);
        $vat = $this->parseCurrency($req->to_update['vat'] ?? null);
        // $client_vat = 0; //initialize client_vat to 0
        // if($projectCost > 0){
        //     $firstCollectionPercentage = ($firstCollection / $projectCost) * 100;
        //     //check if the 30% down payment condition is met
        //     if($firstCollectionPercentage >= 30){
        //         $client_vat = $projectCost * 0.02; //calculate the 2% client_vat
        //     }
        // }
        $totalCollections =
            $this->parseCurrency($req->to_update['first_collection'] ?? null); +
            $this->parseCurrency($req->to_update['second_collection'] ?? null) +
            $this->parseCurrency($req->to_update['third_collection'] ?? null) +
            $this->parseCurrency($req->to_update['fourth_collection'] ?? null);
        
        //subtract the client_vat from the project cost before calculating the receivable balance
        $receivableBal = $projectCost - ($totalCollections + $withholdingTaxAmount);
        if($receivableBal < 0) $receivableBal = 0;


        // $receivableBal = $projectCost - $totalCollections;
        // if($receivableBal < 0) $receivableBal = 0;

        $insert = ConstructionSalesRevenue::create([
            'date' => $formatDate($req->to_update['date'] ?? null), 
            'client_name_address' => $req->to_update['client_name_address'] ?? null, 
            'particulars' => $req->to_update['particulars'] ?? null, 
            'status_of_vat' => $req->to_update['status_of_vat'] ?? null, 
            'receipt_no' => $req->to_update['receipt_no'] ?? null, 
            'project_cost' => $projectCost,
            'amount_gross_of_vat' => $grossVat,
            'net_of_vat' => $netVat,
            'vat' => $vat,
            'first_date_of_collection' => $formatDate($req->to_update['first_date_of_collection'] ?? null), 
            'first_collection' => $this->parseCurrency($req->to_update['first_collection'] ?? null), 
            'second_date_of_collection' => $formatDate($req->to_update['second_date_of_collection'] ?? null), 
            'second_collection' => $this->parseCurrency($req->to_update['second_collection'] ?? null), 
            'third_date_of_collection' => $formatDate($req->to_update['third_date_of_collection'] ?? null), 
            'third_collection' => $this->parseCurrency($req->to_update['third_collection'] ?? null), 
            'fourth_date_of_collection' => $formatDate($req->to_update['fourth_date_of_collection'] ?? null), 
            'fourth_collection' => $this->parseCurrency($req->to_update['fourth_collection'] ?? null),
            'total' => $this->parseCurrency($req->to_update['total'] ?? null),
            'receivable_bal' => $receivableBal, 
            'others' => $req->to_update['others'] ?? null, 
            'remarks' => $req->to_update['remarks'] ?? null, 
            'fully_paid_date' => $formatDate($req->to_update['fully_paid_date'] ?? null),
            'withholding_tax' => $withholdingTaxStr, //store as string
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
        $grossVat = $this->parseCurrency($req->to_update['amount_gross_of_vat'] ?? null);
        $netVat = $this->parseCurrency($req->to_update['net_of_vat'] ?? null);
        $vat = $this->parseCurrency($req->to_update['vat'] ?? null);
        $totalCollections =
            $this->parseCurrency($req->to_update['first_collection'] ?? null) +
            $this->parseCurrency($req->to_update['second_collection'] ?? null) +
            $this->parseCurrency($req->to_update['third_collection'] ?? null) +
            $this->parseCurrency($req->to_update['fourth_collection'] ?? null);
        $receivableBal = $projectCost - ($totalCollections + $withholdingTaxAmount);
        if($receivableBal < 0) $receivableBal = 0;

        $record = ConstructionSalesRevenue::find($req->to_update['id']);

        $record->update([
            'date' => $formatDate($req->to_update['date'] ?? null), 
            'client_name_address' => $req->to_update['client_name_address'] ?? null, 
            'particulars' => $req->to_update['particulars'] ?? null, 
            'status_of_vat' => $req->to_update['status_of_vat'] ?? null, 
            'receipt_no' => $req->to_update['receipt_no'] ?? null, 
            'project_cost' => $projectCost,
            'amount_gross_of_vat' => $grossVat,
            'net_of_vat' => $netVat,
            'vat' => $vat,
            'first_date_of_collection' => $formatDate($req->to_update['first_date_of_collection'] ?? null), 
            'first_collection' => $this->parseCurrency($req->to_update['first_collection'] ?? null), 
            'second_date_of_collection' => $formatDate($req->to_update['second_date_of_collection'] ?? null), 
            'second_collection' => $this->parseCurrency($req->to_update['second_collection'] ?? null), 
            'third_date_of_collection' => $formatDate($req->to_update['third_date_of_collection'] ?? null), 
            'third_collection' => $this->parseCurrency($req->to_update['third_collection'] ?? null), 
            'fourth_date_of_collection' => $formatDate($req->to_update['fourth_date_of_collection'] ?? null), 
            'fourth_collection' => $this->parseCurrency($req->to_update['fourth_collection'] ?? null),
            'total' => $this->parseCurrency($req->to_update['total'] ?? null),
            'receivable_bal' => $receivableBal, 
            'others' => $req->to_update['others'] ?? null, 
            'remarks' => $req->to_update['remarks'] ?? null, 
            'fully_paid_date' => $formatDate($req->to_update['fully_paid_date'] ?? null),
            'withholding_tax' => $withholdingTaxStr, // store as string
        ]);

        return response()->json([
            'message' => 'Sales revenue data updated successfully!',
            'data' => $record,
        ], 200);
    }

    public function monthlyCosts(){
        $currentYear = date('Y');
        
        $results = DB::table('construction_sales_revenue')
            ->select(
                DB::raw("DATE_FORMAT(date, '%b') as month"),
                DB::raw('SUM(project_cost) as total_cost')
            )
            ->whereYear('date', $currentYear)
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
        
        //check if there are any sales records in the database
        $totalSalesRecords = DB::table('construction_sales_revenue')->count();
        $salesWithDates = DB::table('construction_sales_revenue')->whereNotNull('date')->count();
        $salesThisYear = DB::table('construction_sales_revenue')->whereYear('date', $currentYear)->count();
        
        Log::info('Sales counts:', [
            'total' => $totalSalesRecords,
            'with_dates' => $salesWithDates,
            'this_year' => $salesThisYear
        ]);
        
        $totalSales = DB::table('construction_sales_revenue')
            ->select(DB::raw('SUM(project_cost) as yearly_total'))
            ->whereRaw('YEAR(date) = ?', [$currentYear])
            ->first();

        Log::info('YearlySales query result:', ['result' => $totalSales, 'currentYear' => $currentYear]);

        return response()->json([
            'year' => $currentYear,
            'total_sales' => $totalSales->yearly_total ?: 0
        ]);
    }
}
