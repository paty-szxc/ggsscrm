<?php

namespace App\Http\Controllers;

use App\Imports\VoucherImport;
use App\Imports\VouchersMultiSheetImport;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data = Voucher::orderBy('date', 'desc')->get();

        $data->transform(function($item){
            $item->employee_salary = number_format((float) $item->employee_salary, 2) !== '0.00' ? number_format((float) $item->employee_salary, 2) : '';
            $item->employee_benefits = number_format((float) $item->employee_benefits, 2) !== '0.00' ? number_format((float) $item->employee_benefits, 2) : '';
            $item->meals_office_survey = number_format((float) $item->meals_office_survey, 2) !== '0.00' ? number_format((float) $item->meals_office_survey, 2) : '';
            $item->dog_food = number_format((float) $item->dog_food, 2) !== '0.00' ? number_format((float) $item->dog_food, 2) : '';
            $item->construction_survey_supplies = number_format((float) $item->construction_survey_supplies, 2) !== '0.00' ? number_format((float) $item->construction_survey_supplies, 2) : '';
            $item->repairs_maintenance = number_format((float) $item->repairs_maintenance, 2) !== '0.00' ? number_format((float) $item->repairs_maintenance, 2) : '';
            $item->office_supplies = number_format((float) $item->office_supplies, 2) !== '0.00' ? $item->office_supplies : '';
            $item->gasoline_oil = number_format((float) $item->gasoline_oil, 2) !== '0.00' ? $item->gasoline_oil : '';
            $item->utilities = number_format((float) $item->utilities, 2) !== '0.00' ? $item->utilities : '';
            $item->parking_fee = number_format((float) $item->parking_fee, 2) !== '0.00' ? $item->parking_fee : '';
            $item->toll_fee = number_format((float) $item->toll_fee, 2) !== '0.00' ? $item->toll_fee : '';
            $item->permits_certification_tax = number_format((float) $item->permits_certification_tax, 2) !== '0.00' ? number_format((float) $item->permits_certification_tax, 2) : '';
            $item->transportation = number_format((float) $item->transportation, 2) !== '0.00' ? number_format((float) $item->transportation, 2) : '';
            $item->budget = number_format((float) $item->budget, 2) !== '0.00' ? number_format((float) $item->budget, 2) : '';
            $item->representation_expense_personal = number_format((float) $item->representation_expense_personal, 2) !== '0.00' ? number_format((float) $item->representation_expense_personal, 2) : '';
            $item->others_staff_personal = number_format((float) $item->others_staff_personal, 2) !== '0.00' ? number_format((float) $item->others_staff_personal, 2) : '';
            $item->amount_gross_of_vat = number_format((float) $item->amount_gross_of_vat, 2) !== '0.00' ? number_format((float) $item->amount_gross_of_vat, 2) : '';
            $item->net_of_vat = number_format((float) $item->net_of_vat, 2) !== '0.00' ? number_format((float) $item->net_of_vat, 2) : '';
            $item->vat = number_format((float) $item->vat, 2) !== '0.00' ? number_format((float) $item->vat, 2) : '';
            return $item;
        });
        return $data;
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|  max:10000',
        ]);
        try{
            // Excel::import(new VoucherImport(), $request->file('file'));
            Excel::import(new VouchersMultiSheetImport(), $request->file('file'));
            if($request->wantsJson()){
                return response()->json([
                    'success' => true,
                    'message' => 'Monthly voucher imported successfully!'
                ]);
            }
            return redirect()->back()->with('success', 'Monthly voucher imported successfully!');
        }
        catch(\Exception $e){
            Log::error('Import error: ' . $e->getMessage());
            if($request->wantsJson()){
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import monthly voucher',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function insert(Request $req){
        $formatDate = function($date){
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };
        $data = $req->input('to_update', []);
        $data['date'] = $formatDate($data['date'] ?? null);
        $voucher = Voucher::create($data);
        return response()->json(['success' => true, 'voucher' => $voucher]);
    }

    public function update(Request $req){
        $formatDate = function($date){
            return $date ? date('Y-m-d', strtotime($date)) : null;
        };
        $data = $req->input('to_update', []);
        $id = $data['id'] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID is required for update'], 400);
        }
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found'], 404);
        }
        $data['date'] = $formatDate($data['date'] ?? null);
        $voucher->update($data);
        return response()->json(['success' => true, 'voucher' => $voucher]);
    }

    public function monthlyExpenses(){
        $currentYear = date('Y');
        $results = DB::table('vouchers')
            ->select(
                DB::raw("DATE_FORMAT(date, '%b') as month"),
                DB::raw('SUM(COALESCE(employee_salary, 0)) as salary'),
                DB::raw('SUM(COALESCE(employee_benefits, 0)) as benefits'),
                DB::raw('SUM(COALESCE(meals_office_survey, 0)) as meals'),
                DB::raw('SUM(COALESCE(dog_food, 0)) as dog_food'),
                DB::raw('SUM(COALESCE(construction_survey_supplies, 0)) as construction'),
                DB::raw('SUM(COALESCE(repairs_maintenance, 0)) as repairs'),
                DB::raw('SUM(COALESCE(office_supplies, 0)) as office'),
                DB::raw('SUM(COALESCE(gasoline_oil, 0)) as gas'),
                DB::raw('SUM(COALESCE(utilities, 0)) as utilities'),
                DB::raw('SUM(COALESCE(parking_fee, 0)) as parking'),
                DB::raw('SUM(COALESCE(toll_fee, 0)) as toll'),
                DB::raw('SUM(COALESCE(permits_certification_tax, 0)) as tax'),
                DB::raw('SUM(COALESCE(transportation, 0)) as transpo'),
                DB::raw('SUM(COALESCE(budget, 0)) as budget'),
                DB::raw('SUM(
                    COALESCE(employee_salary, 0) + 
                    COALESCE(employee_benefits, 0) + 
                    COALESCE(meals_office_survey, 0) + 
                    COALESCE(dog_food, 0) + 
                    COALESCE(construction_survey_supplies, 0) + 
                    COALESCE(repairs_maintenance, 0) + 
                    COALESCE(office_supplies, 0) + 
                    COALESCE(gasoline_oil, 0) + 
                    COALESCE(utilities, 0) + 
                    COALESCE(parking_fee, 0) + 
                    COALESCE(toll_fee, 0) + 
                    COALESCE(permits_certification_tax, 0) + 
                    COALESCE(transportation, 0) + 
                    COALESCE(budget, 0)
                ) as grand_total')
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
                'categories' => [
                    'salary' => $found ? $found->salary : 0,
                    'benefits' => $found ? $found->benefits : 0,
                    'meals' => $found ? $found->meals : 0,
                    'dog_food' => $found ? $found->dog_food : 0,
                    'construction' => $found ? $found->construction : 0,
                    'repairs' => $found ? $found->repairs : 0,
                    'office' => $found ? $found->office : 0,
                    'gas' => $found ? $found->gas : 0,
                    'utilities' => $found ? $found->utilities : 0,
                    'parking' => $found ? $found->parking : 0,
                    'toll' => $found ? $found->toll : 0,
                    'tax' => $found ? $found->tax : 0,
                    'transpo' => $found ? $found->transpo : 0,
                    'budget' => $found ? $found->budget : 0,
                ],
                'grand_total' => $found ? $found->grand_total : 0
            ];
        });

        return response()->json($data);
    }

    public function yearlyExpenses(){
        $currentYear = date('Y');
        
        $result = DB::table('vouchers')
            ->select(
                DB::raw('SUM(COALESCE(employee_salary, 0)) as salary'),
                DB::raw('SUM(COALESCE(employee_benefits, 0)) as benefits'),
                DB::raw('SUM(COALESCE(meals_office_survey, 0)) as meals'),
                DB::raw('SUM(COALESCE(dog_food, 0)) as dog_food'),
                DB::raw('SUM(COALESCE(construction_survey_supplies, 0)) as construction'),
                DB::raw('SUM(COALESCE(repairs_maintenance, 0)) as repairs'),
                DB::raw('SUM(COALESCE(office_supplies, 0)) as office'),
                DB::raw('SUM(COALESCE(gasoline_oil, 0)) as gas'),
                DB::raw('SUM(COALESCE(utilities, 0)) as utilities'),
                DB::raw('SUM(COALESCE(parking_fee, 0)) as parking'),
                DB::raw('SUM(COALESCE(toll_fee, 0)) as toll'),
                DB::raw('SUM(COALESCE(permits_certification_tax, 0)) as tax'),
                DB::raw('SUM(COALESCE(transportation, 0)) as transpo'),
                DB::raw('SUM(COALESCE(budget, 0)) as budget'),
                DB::raw('SUM(
                    COALESCE(employee_salary, 0) + 
                    COALESCE(employee_benefits, 0) + 
                    COALESCE(meals_office_survey, 0) + 
                    COALESCE(dog_food, 0) + 
                    COALESCE(construction_survey_supplies, 0) + 
                    COALESCE(repairs_maintenance, 0) + 
                    COALESCE(office_supplies, 0) + 
                    COALESCE(gasoline_oil, 0) + 
                    COALESCE(utilities, 0) + 
                    COALESCE(parking_fee, 0) + 
                    COALESCE(toll_fee, 0) + 
                    COALESCE(permits_certification_tax, 0) + 
                    COALESCE(transportation, 0) + 
                    COALESCE(budget, 0)
                ) as grand_total')
            )
            ->whereYear('date', $currentYear)
            ->first();

        return response()->json([
            'year' => $currentYear,
            'categories' => [
                'salary' => $result->salary ?? 0,
                'benefits' => $result->benefits ?? 0,
                'meals' => $result->meals ?? 0,
                'dog_food' => $result->dog_food ?? 0,
                'construction' => $result->construction ?? 0,
                'repairs' => $result->repairs ?? 0,
                'office' => $result->office ?? 0,
                'gas' => $result->gas ?? 0,
                'utilities' => $result->utilities ?? 0,
                'parking' => $result->parking ?? 0,
                'toll' => $result->toll ?? 0,
                'tax' => $result->tax ?? 0,
                'transpo' => $result->transpo ?? 0,
                'budget' => $result->budget ?? 0,
            ],
            'grand_total' => $result->grand_total ?? 0
        ]);
    }
}
