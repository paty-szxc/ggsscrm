<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Imports\SurveysImport;
use Maatwebsite\Excel\Facades\Excel;

class SurveyController extends Controller
{
    public function index(){
        $survey = Survey::orderBy('date_started', 'asc')->get();
        return $survey;
    }

    public function import(Request $req){
        $req->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new SurveysImport, $req->file('file'));

        return redirect()->back()->with('success', 'Surveys imported successfully!');
    }

    public function insert(Request $req){
        // return $req;
        $insert = Survey::create([
            'date_started' => $req->to_update['date_started'],
            'date_completed' => $req->to_update['date_completed'],
            'location' => $req->to_update['location'],
            'survey_details' => $req->to_update['survey_details'],
            'area' => $req->to_update['area'] ?? null, 
            'processed_by' => $req->to_update['processed_by'] ?? null, 
            'survey' => isset($req->to_update['survey']) ? $req->to_update['survey'] : 0, 
            'data_process' => isset($req->to_update['data_process']) ? $req->to_update['data_process'] : 0,
            'plans' => isset($req->to_update['plans']) ? $req->to_update['plans'] : 0,
            'remarks' => $req->to_update['remarks'] ?? null,
            'contact_person' => $req->to_update['contact_person'] ?? null,
            'contact_no' => $req->to_update['contact_no'] ?? null, 
            'date_approved' => $req->to_update['date_approved'], 
            'date_delivered' => $req->to_update['date_delivered'],
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
            'date_completed' => $req->to_update['date_completed'],
            'location' => $req->to_update['location'],
            'survey_details' => $req->to_update['survey_details'],
            'area' => $req->to_update['area'],
            'processed_by' => $req->to_update['processed_by'],
            'survey' => isset($req->to_update['survey']) ? $req->to_update['survey'] : 0,
            'data_process' => isset($req->to_update['data_process']) ? $req->to_update['data_process'] : 0,
            'plans' => isset($req->to_update['plans']) ? $req->to_update['plans'] : 0,
            'remarks' => $req->to_update['remarks'],
            'contact_person' => $req->to_update['contact_person'],
            'contact_no' => $req->to_update['contact_no'],
            'date_approved' => $req->to_update['date_approved'],
            'date_delivered' => $req->to_update['date_delivered'],
        ]);
    }
}
