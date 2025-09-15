<?php

namespace App\Http\Controllers;

use App\Models\SurveyEquipments;
use App\Models\SurveyEquipmentMovement;
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

    // Incoming/Outgoing Equipment Methods
    public function getIO(){
        return SurveyEquipmentMovement::all();
    }

    public function insertIO(Request $req){
        $validator = Validator::make($req->all(), [
            'to_update.description' => 'required|array',
            'to_update.description.*' => 'required|string',
            'to_update.site' => 'nullable|string',
            'to_update.handled_by' => 'nullable|string',
            'to_update.incoming_date' => 'nullable|date',
            'to_update.outgoing_date' => 'nullable|date',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $req->to_update;
        $descriptions = is_array($data['description']) ? $data['description'] : [$data['description']];
        
        $insertedRecords = [];
        
        // Create a record for each selected description
        foreach($descriptions as $description) {
            $insert = SurveyEquipmentMovement::create([
                'description' => $description,
                'site' => $data['site'] ?? null,
                'handled_by' => $data['handled_by'] ?? null,
                'incoming_date' => $data['incoming_date'] ?? null,
                'outgoing_date' => $data['outgoing_date'] ?? null,
            ]);
            $insertedRecords[] = $insert;
        }

        return response()->json([
            'message' => 'Equipment movement(s) inserted successfully!',
            'data' => $insertedRecords,
        ], 201);
    }

    public function updateIO(Request $req){
        $validator = Validator::make($req->all(), [
            'to_update.description' => 'required|array',
            'to_update.description.*' => 'required|string',
            'to_update.site' => 'nullable|string',
            'to_update.handled_by' => 'nullable|string',
            'to_update.incoming_date' => 'nullable|date',
            'to_update.outgoing_date' => 'nullable|date',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $req->to_update;
        $descriptions = is_array($data['description']) ? $data['description'] : [$data['description']];
        
        // For editing, we'll update the first description and create new records for additional ones
        $movement = SurveyEquipmentMovement::findOrFail($req->to_update['id']);
        
        // Update the existing record with the first description
        $movement->update([
            'description' => $descriptions[0],
            'site' => $data['site'] ?? $movement->site,
            'handled_by' => $data['handled_by'] ?? $movement->handled_by,
            'incoming_date' => $data['incoming_date'] ?? $movement->incoming_date,
            'outgoing_date' => $data['outgoing_date'] ?? $movement->outgoing_date,
        ]);

        $updatedRecords = [$movement];
        
        // Create new records for additional descriptions
        for($i = 1; $i < count($descriptions); $i++) {
            $newRecord = SurveyEquipmentMovement::create([
                'description' => $descriptions[$i],
                'site' => $data['site'] ?? null,
                'handled_by' => $data['handled_by'] ?? null,
                'incoming_date' => $data['incoming_date'] ?? null,
                'outgoing_date' => $data['outgoing_date'] ?? null,
            ]);
            $updatedRecords[] = $newRecord;
        }

        return response()->json([
            'message' => 'Equipment movement(s) updated successfully!',
            'data' => $updatedRecords,
        ]);
    }
}
