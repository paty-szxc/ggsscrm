<?php

namespace App\Http\Controllers;

use App\Models\SurveyGovernmentRelated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyGovernmentRelatedController extends Controller
{
    public function index(){
        return SurveyGovernmentRelated::orderBy('date_started', 'desc')->get();
    }

    public function getCurrentUser(){
        $user = auth()->user();
        return response()->json([ 
            'id' => $user->id,
            'username' => $user->username
        ]);
    }

    public function insert(Request $req){
        $userId = Auth::id();
        //calculate duration if start_actual and date_completed are provided
        $duration = null;
        if(!empty($req->to_update['date_started']) && !empty($req->to_update['date_completed'])){
            $startDate = new \DateTime($req->to_update['date_started']);
            $endDate = new \DateTime($req->to_update['date_completed']);
            $interval = $startDate->diff($endDate);
            $duration = $interval->days;
        }

        $insert = SurveyGovernmentRelated::create([
            'user_id' => $userId,
            'date_started' => $req->to_update['date_started'],
            'client' => $req->to_update['client' ?? null],
            'location' => $req->to_update['location'] ?? null,
            'type_of_plan_survey' => $req->to_update['type_of_plan_survey'] ?? null,
            'date_completed' => $req->to_update['date_completed'] ?? null,
            'duration' => $duration,
            'remarks' => $req->to_update['remarks'] ?? null,
        ]);
        return response()->json([
            'message' => 'Data inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        //calculate duration if start_actual and date_completed are provided
        $duration = null;
        if(!empty($req->to_update['date_started']) && !empty($req->to_update['date_completed'])){
            $startDate = new \DateTime($req->to_update['date_started']);
            $endDate = new \DateTime($req->to_update['date_completed']);
            $interval = $startDate->diff($endDate);
            $duration = $interval->days;
        }

        $update = SurveyGovernmentRelated::find($req->to_update['id']);
        $update->update([
            'date_started' => $req->to_update['date_started'],
            'client' => $req->to_update['client' ?? null],
            'location' => $req->to_update['location'] ?? null,
            'type_of_plan_survey' => $req->to_update['type_of_plan_survey'] ?? null,
            'date_completed' => $req->to_update['date_completed'] ?? null,
            'duration' => $duration,
            'remarks' => $req->to_update['remarks'] ?? null,
        ]);
        return response()->json([
            'message' => 'Data updated successfully!',
            'data' => $update
        ]);
    }
}
