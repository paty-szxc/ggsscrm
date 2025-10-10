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

    private function calculateBusinessDays(\DateTime $startDate, \DateTime $endDate){
        $interval = $startDate->diff($endDate);
        $totalDays = $interval->days;

        //if start date is after end date, return 0 (or handle as error)
        if ($startDate > $endDate) {
            return 0;
        }

        $businessDays = 0;
        $currentDate = clone $startDate;

        while($currentDate <= $endDate){
            // Check if the current day is NOT a Sunday (Sunday is '0' or '7' depending on format, '0' is common for DateTime::format('w'))
            // We use 'N' (ISO-8601 numeric representation of the day of the week, 1 for Monday through 7 for Sunday)
            $dayOfWeek = $currentDate->format('N'); 
            
            //exclude Sunday (7)
            if($dayOfWeek != 7){ 
                $businessDays++;
            }
            
            //move to the next day
            $currentDate->modify('+1 day');
        }

        // Since your backend logic calculates duration as (end date - start date) days, 
        // it seems you want the number of days *between* them, not including the start day.
        // If you want the number of days *elapsed* including the start and end, use $businessDays.
        // If you want the difference (total duration in days), use $businessDays - 1 if the dates are non-consecutive, but since you are counting, we will stick to the count.

        //return the total count of non-Sunday days
        return $businessDays;
    }

    public function insert(Request $req){
        $userId = Auth::id();
        $duration = null;
        $permitDuration = null;
        $constructionDuration = null;

        //total duration calculation
        //condition: Check for start_process AND date_completed
        if(!empty($req->to_update['start_process']) && !empty($req->to_update['date_completed'])){
            $startDate = new \DateTime($req->to_update['start_process']);
            $endDate = new \DateTime($req->to_update['date_completed']);
            $duration = $this->calculateBusinessDays($startDate, $endDate);
        }

        //permit duration calculation
        //condition: Check for start_process AND end_process
        if(!empty($req->to_update['start_process']) && !empty($req->to_update['end_process'])){
            $permitStart = new \DateTime($req->to_update['start_process']);
            $permitEnd = new \DateTime($req->to_update['end_process']);
            $permitDuration = $this->calculateBusinessDays($permitStart, $permitEnd);
        }

        //construction duration calculation
        //condition: Check for start_actual AND end_actual
        if(!empty($req->to_update['start_actual']) && !empty($req->to_update['end_actual'])){
            $cnstrctnStart = new \DateTime($req->to_update['start_actual']);
            $cnstrctnEnd = new \DateTime($req->to_update['end_actual']);
            $constructionDuration = $this->calculateBusinessDays($cnstrctnStart, $cnstrctnEnd);
        }

        $insert = ConstructionProjects::create([
            'user_id' => $userId,
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'particulars' => $req->to_update['particulars'],
            'processed_by' => $req->to_update['processed_by'] ?? null, 
            'start_process' => $req->to_update['start_process'] ?? null, 
            'end_process' => $req->to_update['end_process'] ?? null,
            'permit_duration' => $permitDuration,
            'start_actual' => $req->to_update['start_actual'] ?? null,
            'end_actual' => $req->to_update['end_actual'] ?? null,
            'construction_duration' => $constructionDuration,
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
        $duration = null;
        $permitDuration = null;
        $constructionDuration = null;
        //total duration calculation
        //condition: Check for start_process AND date_completed
        if(!empty($req->to_update['start_process']) && !empty($req->to_update['date_completed'])){
            $startDate = new \DateTime($req->to_update['start_process']);
            $endDate = new \DateTime($req->to_update['date_completed']);
            $duration = $this->calculateBusinessDays($startDate, $endDate);
        }

        //permit duration calculation
        //condition: Check for start_process AND end_process
        if(!empty($req->to_update['start_process']) && !empty($req->to_update['end_process'])){
            $permitStart = new \DateTime($req->to_update['start_process']);
            $permitEnd = new \DateTime($req->to_update['end_process']);
            $permitDuration = $this->calculateBusinessDays($permitStart, $permitEnd);
        }

        //construction duration calculation
        //condition: Check for start_actual AND end_actual
        if(!empty($req->to_update['start_actual']) && !empty($req->to_update['end_actual'])){
            $cnstrctnStart = new \DateTime($req->to_update['start_actual']);
            $cnstrctnEnd = new \DateTime($req->to_update['end_actual']);
            $constructionDuration = $this->calculateBusinessDays($cnstrctnStart, $cnstrctnEnd);
        }

        $update = ConstructionProjects::find($req->to_update['id']);
        $update->update([
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'particulars' => $req->to_update['particulars'],
            'processed_by' => $req->to_update['processed_by'], 
            'start_process' => $req->to_update['start_process'], 
            'end_process' => $req->to_update['end_process'],
            'permit_duration' => $permitDuration,
            'start_actual' => $req->to_update['start_actual'],
            'end_actual' => $req->to_update['end_actual'],
            'construction_duration' => $constructionDuration,
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
