<?php

namespace App\Http\Controllers;

use App\Imports\ConstructionProjectImport;
use Illuminate\Http\Request;
use App\Models\ConstructionProjects;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ConstructionProjectsController extends Controller
{
    public function index(){
        $user = auth()->user();
        if(in_array($user->id, [1, 3])){
            return ConstructionProjects::orderBy('date_started', 'desc')->get();
        } 
        else{
            return ConstructionProjects::where('user_id', $user->id)
                ->orderBy('date_started', 'desc')
                ->get();
        }
    }

    public function getCurrentUser(){
        $user = auth()->user();
        return response()->json([ 
            'id' => $user->id,
            'username' => $user->username
        ]);
    }

    public function import(Request $req){
        $req->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', //added max file size (10MB)
        ]);

        try{
            $file = $req->file('file');
            Excel::import(new ConstructionProjectImport, $file);
            return back()->with('success', 'Construction projects imported successfully!');
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
        $userId = Auth::id();
        //calculate duration if start_actual and date_completed are provided
        $duration = null;
        if(!empty($req->to_update['start_actual']) && !empty($req->to_update['date_completed'])){
            $startDate = new \DateTime($req->to_update['start_actual']);
            $endDate = new \DateTime($req->to_update['date_completed']);
            $interval = $startDate->diff($endDate);
            $duration = $interval->days;
        }

        $insert = ConstructionProjects::create([
            'user_id' => $userId,
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'particulars' => $req->to_update['particulars'],
            'processed_by' => $req->to_update['processed_by'] ?? null, 
            'start_process' => $req->to_update['start_process'] ?? null, 
            'end_process' => $req->to_update['end_process'] ?? null,
            'start_actual' => $req->to_update['start_actual'] ?? null,
            'end_actual' => $req->to_update['end_actual'] ?? null,
            'date_completed' => $req->to_update['date_completed'] ?? null,
            'duration' => $duration,
            'contact_person' => $req->to_update['contact_person'] ?? null,
            'contact_no' => $req->to_update['contact_no'] ?? null, 
            'remarks' => $req->to_update['remarks'] ?? null,
        ]);
        return response()->json([
            'message' => 'Construction project data inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        //calculate duration if start_actual and date_completed are provided
        $duration = null;
        if(!empty($req->to_update['start_actual']) && !empty($req->to_update['date_completed'])){
            $startDate = new \DateTime($req->to_update['start_actual']);
            $endDate = new \DateTime($req->to_update['date_completed']);
            $interval = $startDate->diff($endDate);
            $duration = $interval->days;
        }
        $update = ConstructionProjects::find($req->to_update['id']);
        $update->update([
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'particulars' => $req->to_update['particulars'],
            'processed_by' => $req->to_update['processed_by'], 
            'start_process' => $req->to_update['start_process'], 
            'end_process' => $req->to_update['end_process'],
            'start_actual' => $req->to_update['start_actual'],
            'end_actual' => $req->to_update['end_actual'],
            'date_completed' => $req->to_update['date_completed'],
            'duration' => $duration,
            'contact_person' => $req->to_update['contact_person'],
            'contact_no' => $req->to_update['contact_no'], 
            'remarks' => $req->to_update['remarks'],
        ]);
        return response()->json([
            'message' => 'Construction data updated successfully!',
            'data' => $update
        ]);
    }
}
