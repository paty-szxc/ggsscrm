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
    public function index(){
        $sales_revenue = SalesRevenue::orderBy('date_of_survey', 'desc')->get();

        $sales_revenue->transform(function($item){
            $item->project_cost = number_format($item->project_cost, 2);
            $item->first_collection = $item->first_collection !== null ? number_format($item->first_collection, 2) : '';
            $item->second_collection =  $item->second_collection !== null ? number_format($item->second_collection, 2) : '';
            $item->third_collection =  $item->third_collection !== null ? number_format($item->third_collection, 2) : '';
            $item->fourth_collection =  $item->fourth_collection !== null ? number_format($item->fourth_collection, 2) : '';
            $item->total =  $item->total !== null ? number_format($item->total, 2) : '';
            $item->receivable_bal =  $item->receivable_bal !== null ? number_format($item->receivable_bal, 2) : '';
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

    public function insert(Request $req) {
        // Format dates to Y-m-d format
        $formatDate = function($date) {
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };

        $insert = SalesRevenue::create([
            'date_of_survey' => $formatDate($req->to_update['date_of_survey'] ?? null), 
            'location' => $req->to_update['location'] ?? null, 
            'type_of_survey' => $req->to_update['type_of_survey'] ?? null, 
            'receipt_no' => $req->to_update['receipt_no'] ?? null, 
            'project_cost' => $req->to_update['project_cost'] ?? null, 
            'first_date_of_collection' => $formatDate($req->to_update['first_date_of_collection'] ?? null), 
            'first_collection' => $req->to_update['first_collection'] ?? null, 
            'second_date_of_collection' => $formatDate($req->to_update['second_date_of_collection'] ?? null), 
            'second_collection' => $req->to_update['second_collection'] ?? null, 
            'third_date_of_collection' => $formatDate($req->to_update['third_date_of_collection'] ?? null), 
            'third_collection' => $req->to_update['third_collection'] ?? null, 
            'fourth_date_of_collection' => $formatDate($req->to_update['fourth_date_of_collection'] ?? null), 
            'fourth_collection' => $req->to_update['fourth_collection'] ?? null,
            'total' => $req->to_update['total'] ?? null,
            'withholding_tax' => $req->to_update['withholding_tax'] ?? null, 
            'receivable_bal' => $req->to_update['receivable_bal'] ?? null, 
            'remarks' => $req->to_update['remarks'] ?? null, 
            'fully_paid_date' => $formatDate($req->to_update['fully_paid_date'] ?? null), 
        ]);

        return response()->json([
            'message' => 'Survey data inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        $formatDate = function($date) {
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };

        $record = SalesRevenue::find($req->to_update['id']);

        $record->update([
            'date_of_survey' => $formatDate($req->to_update['date_of_survey'] ?? null), 
            'location' => $req->to_update['location'] ?? null, 
            'type_of_survey' => $req->to_update['type_of_survey'] ?? null, 
            'receipt_no' => $req->to_update['receipt_no'] ?? null, 
            'project_cost' => $req->to_update['project_cost'] ?? null, 
            'first_date_of_collection' => $formatDate($req->to_update['first_date_of_collection'] ?? null), 
            'first_collection' => $req->to_update['first_collection'] ?? null, 
            'second_date_of_collection' => $formatDate($req->to_update['second_date_of_collection'] ?? null), 
            'second_collection' => $req->to_update['second_collection'] ?? null, 
            'third_date_of_collection' => $formatDate($req->to_update['third_date_of_collection'] ?? null), 
            'third_collection' => $req->to_update['third_collection'] ?? null, 
            'fourth_date_of_collection' => $formatDate($req->to_update['fourth_date_of_collection'] ?? null), 
            'fourth_collection' => $req->to_update['fourth_collection'] ?? null,
            'total' => $req->to_update['total'] ?? null,
            'withholding_tax' => $req->to_update['withholding_tax'] ?? null, 
            'receivable_bal' => $req->to_update['receivable_bal'] ?? null, 
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
}
