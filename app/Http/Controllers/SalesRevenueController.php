<?php

namespace App\Http\Controllers;

use App\Imports\SalesRevenueImport;
use App\Models\SalesRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SalesRevenueController extends Controller
{
    public function index(){
        $sales_revenue = SalesRevenue::orderBy('date_of_survey', 'asc')->get();

        $sales_revenue->transform(function ($item) {
            $item->project_cost = number_format($item->project_cost, 2);
            $item->first_collection = number_format($item->first_collection, 2);
            $item->second_collection = number_format($item->second_collection, 2);
            $item->third_collection = number_format($item->third_collection, 2);
            $item->receivable_bal = number_format($item->receivable_bal, 2);
            return $item;
        });
        return $sales_revenue;
    }

    // public function import(Request $req){
    //     $req->validate([
    //         'file' => 'required|mimes:xlsx,csv,xls',
    //     ]);

    //     Excel::import(new SalesRevenueImport    (), $req->file('file'));

    //     return redirect()->back()->with('success', 'Sales or Revenue imported successfully!');
    // }
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
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
}
