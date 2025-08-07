<?php

namespace App\Http\Controllers;

use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleMaintenanceController extends Controller
{
    public function index(Request $request){
        if ($request->has('vehicle_id')) {
            return VehicleMaintenance::where('company_vehicle_id', $request->vehicle_id)->get();
        }
        return VehicleMaintenance::all();
    }

    public function insert(Request $request){
        $validatedData = $request->validate([
            'company_vehicle_id' => 'required|exists:company_vehicle,id',
            'date' => 'required|date',
            'particulars' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'materials' => 'nullable|string|max:255',
        ]);

        try{
            $maintenance = VehicleMaintenance::create($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Maintenance record created successfully',
                'data' => $maintenance
            ], 201);
        } 
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to create maintenance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id){
        try{
            $maintenance = VehicleMaintenance::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $maintenance
            ], 200);
        } 
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Maintenance record not found'
            ], 404);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve maintenance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id){
        $validatedData = $request->validate([
            'company_vehicle_id' => 'required|exists:company_vehicle,id',
            'date' => 'required|date',
            'particulars' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'materials' => 'nullable|string|max:255',
        ]);

        try{
            $maintenance = VehicleMaintenance::findOrFail($id);
            $maintenance->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Maintenance record updated successfully',
                'data' => $maintenance
            ], 200);
        } 
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return response()->json([
                'success' => false,
                'message' => 'Maintenance record not found',
                'error' => 'The maintenance record you are trying to update does not exist.'
            ], 404);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to update maintenance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
