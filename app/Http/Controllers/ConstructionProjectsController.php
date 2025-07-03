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

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ConstructionProjectImport, $request->file('file'));
        return back()->with('success', 'Construction projects imported successfully!');
    }

    private function calculateDuration($start, $end) {
        if ($start && $end) {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            return $startDate->diff($endDate)->days;
        }
        return null;
    }

    public function insert(Request $req){
        $userId = Auth::id();
        $dateStarted = $req->to_update['date_started'] ?? null;
        $dateCompleted = $req->to_update['date_completed'] ?? null;
        $duration = $this->calculateDuration($dateStarted, $dateCompleted);
        $insert = ConstructionProjects::create([
            'user_id' => $userId,
            'date_started' => $dateStarted,
            'date_completed' => $dateCompleted,
            'client' => $req->to_update['client'] ?? null,
            'location' => $req->to_update['location'] ?? null,
            'type_of_plan_survey' => $req->to_update['type_of_plan_survey'] ?? null,
            'duration' => $duration,
            'remarks' => $req->to_update['remarks'] ?? null,
        ]);
        return response()->json([
            'message' => 'Construction data inserted successfully!',
            'data' => $insert
        ]);
    }

    public function update(Request $req){
        $update = ConstructionProjects::find($req->to_update['id']);
        $dateStarted = $req->to_update['date_started'] ?? $update->date_started;
        $dateCompleted = $req->to_update['date_completed'] ?? $update->date_completed;
        $duration = $this->calculateDuration($dateStarted, $dateCompleted);
        $update->update([
            'date_started' => $dateStarted,
            'date_completed' => $dateCompleted,
            'client' => $req->to_update['client'] ?? $update->client,
            'location' => $req->to_update['location'] ?? $update->location,
            'type_of_plan_survey' => $req->to_update['type_of_plan_survey'],
            'duration' => $duration,
            'remarks' => $req->to_update['remarks'] ?? $update->remarks,
        ]);
        return response()->json([
            'message' => 'Construction data updated successfully!',
            'data' => $update
        ]);
    }
}
