<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Imports\SurveysImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SurveyController extends Controller
{
    public function index(Request $req){
        $user = auth()->user();
        $showAll = $req->input('show_all', 1);
        if(($user->id == 1 || $user->id == 3) && $showAll){
            return Survey::orderBy('date_started', 'desc')->get();
        }
        else{
            return Survey::where('user_id', $user->id)
            ->orderBy('date_started', 'asc')
            ->get();
        }
    }

    public function getCurrentUser(){
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
        ]); 
    }

    public function import(Request $req) {
        $req->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', // Added max file size (10MB)
        ]);

        try {
            Log::info('Starting survey import');
            
            $file = $req->file('file');
            Log::info('Importing file: ' . $file->getClientOriginalName());
            
            Excel::import(new SurveysImport, $file);
            Log::info('Survey import completed successfully');
            
            return redirect()->back()
                ->with('success', 'Surveys imported successfully!');
                
        } catch (\Exception $e) {
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
            'data_process' => isset($req->to_update['data_process']) ? $req->to_update['data_process'] : 0,
            'plans' => isset($req->to_update['plans']) ? $req->to_update['plans'] : 0,
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
            'data_process' => isset($req->to_update['data_process']) ? $req->to_update['data_process'] : 0,
            'plans' => isset($req->to_update['plans']) ? $req->to_update['plans'] : 0,
            'date_approved' => $req->to_update['date_approved'],
            'date_completed' => $req->to_update['date_completed'],
            'remarks' => $req->to_update['remarks'],
            'contact_person' => $req->to_update['contact_person'],
            'contact_no' => $req->to_update['contact_no'],
            'thru' => $req->to_update['thru'],
        ]);
    }
}
