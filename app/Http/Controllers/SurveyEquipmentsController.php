<?php

namespace App\Http\Controllers;

use App\Models\SurveyEquipments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 

class SurveyEquipmentsController extends Controller
{
    public function index(){
        return SurveyEquipments::all();
    }

    public function insert(Request $req){
        // return $req;
        $validator = Validator::make($req->all(), [
            'to_update.description' => 'nullable|string',
            'to_update.price' => 'nullable|numeric',
            'to_update.qty' => 'nullable|string',
            'to_update.serial_no' => 'nullable|string',
            'to_update.date_supplied' => 'nullable|date',
            'to_update.status' => 'nullable|string',
            'to_update.recos' => 'nullable|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $data = $req->to_update;
        
        $insert = SurveyEquipments::create([
            'user_id' => $userId,
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'qty' => $data['qty'] ?? null,
            'serial_no' => $data['serial_no'] ?? null,
            'date_supplied' => $data['date_supplied'] ?? null,
            'status' => $data['status'] ?? null,
            'recos' => $data['recos'] ?? null,
        ]);

        return response()->json([
            'message' => 'Survey equipment inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        $validator = Validator::make($req->all(), [
            'to_update.description' => 'nullable|string',
            'to_update.price' => 'nullable|numeric',
            'to_update.qty' => 'nullable|string',
            'to_update.serial_no' => 'nullable|string',
            'to_update.date_supplied' => 'nullable|date',
            'to_update.status' => 'nullable|string',
            'to_update.recos' => 'nullable|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $equipment = SurveyEquipments::findOrFail($req->to_update['id']);
        $data = $req->to_update;
        
        $equipment->update([
            'description' => $data['description'] ?? $equipment->description,
            'price' => $data['price'] ?? $equipment->price,
            'qty' => $data['qty'] ?? $equipment->qty,
            'serial_no' => $data['serial_no'] ?? $equipment->serial_no,
            'date_supplied' => $data['date_supplied'] ?? $equipment->date_supplied,
            'status' => $data['status'] ?? $equipment->status,
            'recos' => $data['recos'] ?? $equipment->recos,
        ]);

        return response()->json([
            'message' => 'Survey equipment updated successfully!',
            'data' => $equipment,
        ]);
    }
}
