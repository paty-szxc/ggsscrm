<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Imports\SurveysImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SurveyProjectFile;

class SurveyController extends Controller
{
    public function index(Request $req){
        $user = auth()->user();
        $showAll = $req->input('show_all', 1);
        if(($user->role == "Admin") && $showAll){
            return Survey::orderBy('date_started', 'desc')->with('files')->get();
        }
        else{
            return Survey::where('user_id', $user->id)
            ->orderBy('date_started', 'desc')
            ->with('files')
            ->get();
        }
    }

    public function getCurrentUser(){
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'emp_code' =>$user->emp_code,
            'username' => $user->username,
            'role' => $user->role,
        ]); 
    }

    public function import(Request $req) {
        $req->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', // Added max file size (10MB)
        ]);

        try{
            Log::info('Starting survey import');
            
            $file = $req->file('file');
            Log::info('Importing file: ' . $file->getClientOriginalName());
            
            Excel::import(new SurveysImport(), $file);
            Log::info('Survey import completed successfully');
            
            return redirect()->back()
                ->with('success', 'Surveys imported successfully!');
                
        } 
        catch(\Exception $e){
            Log::error('Survey import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
        // return $req;
        $userId = Auth::id();
        $insert = Survey::create([
            'user_id' => $userId,
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'survey_details' => $req->to_update['survey_details'],
            'area' => $req->to_update['area'] ?? null, 
            'processed_by' => $req->to_update['processed_by'] ?? null, 
            'survey' => isset($req->to_update['survey']) ? $req->to_update['survey'] : 0, 
            'data_process' => $req->to_update['data_process'] ?? null,
            'plans' => $req->to_update['plans'] ?? null,
            'date_approved' => $req->to_update['date_approved'] ?? null,
            'date_completed' => $req->to_update['date_completed'] ?? null,
            'remarks' => $req->to_update['remarks'] ?? null,
            'contact_person' => $req->to_update['contact_person'] ?? null,
            'contact_no' => $req->to_update['contact_no'] ?? null, 
            'thru' => $req->to_update['thru'] ?? null,
        ]);
        return response()->json([
            'message' => 'Survey data inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        // return $req;

        // return $req->to_update['remarks'];
        $survey = Survey::find($req->to_update['id']);

        // return $survey;

        $survey->update([
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'survey_details' => $req->to_update['survey_details'],
            'area' => $req->to_update['area'],
            'processed_by' => $req->to_update['processed_by'],
            'survey' => isset($req->to_update['survey']) ? $req->to_update['survey'] : 0,
            'data_process' => $req->to_update['data_process'],
            'plans' => $req->to_update['plans'],
            'date_approved' => $req->to_update['date_approved'],
            'date_completed' => $req->to_update['date_completed'],
            'remarks' => $req->to_update['remarks'],
            'contact_person' => $req->to_update['contact_person'],
            'contact_no' => $req->to_update['contact_no'],
            'thru' => $req->to_update['thru'],
        ]);
    }
    

    public function upload(Request $request){
        // return $request; 
        // return $request->hasFile('files');
        // 1. Validate the incoming request, including the survey_project_id
        $request->validate([
            // 'survey_project_id' => 'required|exists:survey_projects,id', // Ensure it exists in the projects table
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt|max:104857600',
        ], [
            // 'survey_project_id.exists' => 'The selected survey project does not exist.',
            'files.*.mimes' => 'The :attribute must be a file of type: :values.',
            'files.*.max' => 'The :attribute may not be greater than :max kilobytes.',
        ]);

        //make dynamic, if insert get last id if edit get the current id
        $edit_survey_id = $request->input('survey_project_id');
        $lastId = Survey::max('id');
        $surveyProjectId = $edit_survey_id ? $edit_survey_id : $lastId;
        // return $surveyProjectId;
        $uploadedFileDetails = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();
                $storedName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('survey_files', $storedName, 'public');

                //save file metadata to the database, linking it to the survey project
                $fileEntry = SurveyProjectFile::create([
                    'survey_project_id' => $surveyProjectId, // Assign the received ID
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'mime_type' => $mimeType,
                    'size' => $fileSize,
                ]);

                $uploadedFileDetails[] = [
                    'id' => $fileEntry->id,
                    'original_name' => $originalName,
                    'path' => Storage::url($path)
                ];
            }
        }
        return response()->json([
            'message' => 'Files uploaded successfully!',
            'uploaded_files' => $uploadedFileDetails,
        ], 200);
    }

    /**
     * Get yearly chart data including manual data for 2021-2024 and dynamic data for current/next year
     */
    public function getYearlyChartData()
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        // Get manual data for 2021-2024 from config
        $manualData = $this->getManualYearlyData();

        // Get current year data from sales_revenue and vouchers tables
        $currentYearSales = DB::table('sales_revenue')
            ->select(DB::raw('SUM(project_cost) as total_sales'))
            ->whereYear('date_of_survey', $currentYear)
            ->first();

        $currentYearExpenses = DB::table('vouchers')
            ->select(DB::raw('SUM(
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
            ) as total_expenses'))
            ->whereYear('date', $currentYear)
            ->first();

        $currentYearSalesAmount = $currentYearSales->total_sales ?? 0;
        $currentYearExpensesAmount = $currentYearExpenses->total_expenses ?? 0;
        $currentYearProfit = $currentYearSalesAmount - $currentYearExpensesAmount;

        // Build response data
        $allData = [];

        // Add manual years (2021-2024)
        foreach ($manualData as $year => $data) {
            $allData[] = [
                'year' => $year,
                'sales_revenue' => $data['sales_revenue'],
                'expenses' => $data['expenses'],
                'profit' => $data['sales_revenue'] - $data['expenses'],
                'is_manual' => true
            ];
        }

        // Add current year
        $allData[] = [
            'year' => $currentYear,
            'sales_revenue' => $currentYearSalesAmount,
            'expenses' => $currentYearExpensesAmount,
            'profit' => $currentYearProfit,
            'is_manual' => false
        ];

        // Add next year projection
        $nextYearSales = $currentYearSalesAmount * 1.1; // 10% growth projection
        $nextYearExpenses = $currentYearExpensesAmount * 1.05; // 5% growth projection
        $nextYearProfit = $nextYearSales - $nextYearExpenses;

        $allData[] = [
            'year' => $nextYear,
            'sales_revenue' => $nextYearSales,
            'expenses' => $nextYearExpenses,
            'profit' => $nextYearProfit,
            'is_manual' => false
        ];

        return response()->json([
            'success' => true,
            'data' => $allData,
            'current_year' => $currentYear,
            'next_year' => $nextYear
        ]);
    }

    /**
     * Get manual yearly data configuration for 2021-2024
     */
    private function getManualYearlyData()
    {
        return [
            2021 => ['sales_revenue' => 1500000.00, 'expenses' => 800000.00],
            2022 => ['sales_revenue' => 1800000.00, 'expenses' => 950000.00],
            2023 => ['sales_revenue' => 2200000.00, 'expenses' => 1100000.00],
            2024 => ['sales_revenue' => 2500000.00, 'expenses' => 1200000.00],
        ];
    }

    /**
     * Update manual yearly data (for 2021-2024)
     */
    public function updateYearlyData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2021|max:2024',
            'sales_revenue' => 'required|numeric|min:0',
            'expenses' => 'required|numeric|min:0',
        ]);

        $year = $request->year;
        $salesRevenue = $request->sales_revenue;
        $expenses = $request->expenses;
        $profit = $salesRevenue - $expenses;

        // For now, just return success since we're using config
        // In the future, you could store these in a config file or cache
        // To make permanent changes, edit the getManualYearlyData() method in this controller

        return response()->json([
            'success' => true,
            'message' => "Yearly data for $year updated successfully!",
            'data' => [
                'year' => $year,
                'sales_revenue' => $salesRevenue,
                'expenses' => $expenses,
                'profit' => $profit
            ]
        ]);
    }
}
