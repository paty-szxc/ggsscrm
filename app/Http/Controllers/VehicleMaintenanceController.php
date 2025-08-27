<?php

namespace App\Http\Controllers;

use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VehicleMaintenanceController extends Controller
{
    public function index(Request $req){
        if($req->has('vehicle_id')) {
            $maintenance = VehicleMaintenance::where('company_vehicle_id', $req->vehicle_id)->get();
        }
       //append full URL for PDF to each item before returning the response
        foreach($maintenance as $item){
            if($item->file_path){
                $item->maintenance_url = Storage::url($item->file_path);
            }
            else{
                $item->maintenance_url = null;
            }
        }
        return $maintenance;
    }

    public function insert(Request $req){
        try{
            $validated = $req->validate([
                'company_vehicle_id' => 'required|exists:company_vehicle,id',
                'date' => 'required|date',
                'particulars' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'materials' => 'nullable|string|max:255',
                'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760'
            ]);

            $filePath = null;
            //check if a file was uploaded.
            if ($req->hasFile('attachment')) {
                $file = $req->file('attachment');
                $originalFileName = $file->getClientOriginalName(); // Get the original file name

                // Store the file in 'storage/app/public/pdfs/vehicles/maintenance' directory with its original name.
                // The 'public' disk is used, and the path returned is relative to that disk.
                $filePath = $file->storeAs('pdfs/vehicles/maintenance', $originalFileName, 'public');
            }

            $insert = VehicleMaintenance::create([
                'company_vehicle_id' => $validated['company_vehicle_id'],
                'date' => $validated['date'],
                'particulars' => $validated['particulars'],
                'amount' => $validated['amount'],
                'materials' => $validated['materials'],
                'file_path' => $filePath
            ]);

           //generate url
            $insert->pdf_url = $filePath ? Storage::url($filePath) : null;
            
            return response()->json([
                'success' => true,
                'message' => 'Maintenance record created successfully',
                'data' => $insert
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

    public function update(Request $req, $id){
        try{
            $validated = $req->validate([
                'company_vehicle_id' => 'required|exists:company_vehicle,id',
                'date' => 'required|date',
                'particulars' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'materials' => 'nullable|string|max:255',
                'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760'
            ]);

            $maintenance = VehicleMaintenance::findOrFail($id);
            
            $filePath = $maintenance->file_path; //keep current path by default

            //handle file update or removal
            if($req->hasFile('attachment')){
                //delete old file if exists
                if($filePath && Storage::disk('public')->exists($filePath)){
                    Storage::disk('public')->delete($filePath);
                }
                $file = $req->file('attachment');
                $originalFileName = $file->getClientOriginalName();
                //store new file
                $filePath = $file->storeAs('pdfs/vehicles/maintenance', $originalFileName, 'public');
            }

            $maintenance->update([
                'company_vehicle_id' => $validated['company_vehicle_id'],
                'date' => $validated['date'],
                'particulars' => $validated['particulars'],
                'amount' => $validated['amount'],
                'materials' => $validated['materials'],
                'file_path' => $filePath
            ]);

            //append full URL for PDF before returning the response.
            if($maintenance->file_path) {
                $maintenance->pdf_url = Storage::url($maintenance->file_path);
            } 
            else{
                $maintenance->pdf_url = null;
            }
            
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
