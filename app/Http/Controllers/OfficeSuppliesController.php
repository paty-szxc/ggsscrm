<?php

namespace App\Http\Controllers;

use App\Models\CompanyVehicle;
use App\Models\HouseAndLot;
use App\Models\OfficeSupplies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfficeSuppliesController extends Controller
{
    public function index(){
        return OfficeSupplies::all();
    }

    public function insert(Request $req){
        try{
            $validated = $req->validate([
                'item_name' => 'required|string|max:255',
                'item_cost' => [ 'nullable', 'numeric' ],
                'quantity' => 'nullable|integer|max:255',
                'unit' => [ 'nullable', 'string' ],
                'care_of' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:255'
            ]);

            $insert = OfficeSupplies::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'New data inserted successfully!',
                'data' => $insert,
            ], 201);
        } 
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $req){
        try{
            $validated = $req->validate([
                'item_name' => 'required|string|max:255',
                'item_cost' => [ 'nullable', 'numeric' ],
                'quantity' => 'nullable|integer|max:255',
                'unit' => [ 'nullable', 'string' ],
                'care_of' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:255'
            ]);

            $upd = OfficeSupplies::find($req->id);
            $upd->update($validated);
        }
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    //* house and lot functions *//
    public function getHaL(){
        // return HouseAndLot::all();
        $houseAndLots = HouseAndLot::orderBy('address', 'asc')->get();

        //append full URL for PDF to each item before returning the response
        foreach($houseAndLots as $item){
            if($item->pdf_path){
                $item->pdf_url = Storage::url($item->pdf_path);
            }
            else{
                $item->pdf_url = null;
            }
        }
        return $houseAndLots;
    }

    public function insertHaL(Request $req){
        try{
            $validated = $req->validate([
                'address' => 'required|string|max:255',
                'cost' =>  ['nullable', 'numeric'],
                'pdf_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:104857600'
            ]);

            $pdfPath = null;
            //check if a PDF file was uploaded.
            if ($req->hasFile('pdf_file')) {
                $pdfFile = $req->file('pdf_file');
                $originalFileName = $pdfFile->getClientOriginalName(); // Get the original file name

                // Store the PDF in 'storage/app/public/pdfs/house_and_lot' directory with its original name.
                // The 'public' disk is used, and the path returned is relative to that disk.
                $pdfPath = $pdfFile->storeAs('pdfs/house_and_lot', $originalFileName, 'public');
            }

            $insert = HouseAndLot::create([
                'address' => $validated['address'],
                'cost' => $validated['cost'],
                'pdf_path' => $pdfPath,
            ]);

            //append full URL for PDF before returning the response.
            if($insert->pdf_path) {
                $insert->pdf_url = Storage::url($insert->pdf_path);
            } 
            else{
                $insert->pdf_url = null;
            }

            return response()->json([
                'success' => true,
                'message' => 'New data inserted Successfully!',
                'data' => $insert
            ], 201);
        }
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function updateHaL(Request $req, $id){
        try{
            $houseAndLot = HouseAndLot::findOrFail($id);

            $validated = $req->validate([
                'address' => 'string|max:255',
                'cost' => ['nullable', 'numeric', 'min:0'],
                'pdf_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:104857600',
            ], [
                'pdf_file.max' => 'The PDF file must not exceed 10MB',
                'pdf_file.mimes' => 'Only JPG, JPEG, PNG, and PDF files are allowed',
                'cost.numeric' => 'The cost must be a number'
            ]);

            $pdfPath = $houseAndLot->pdf_path; //keep current path by default

            //handle PDF file update or removal
            if($req->hasFile('pdf_file')){
                //delete old PDF if exists
                if($pdfPath && Storage::disk('public')->exists($pdfPath)){
                    Storage::disk('public')->delete($pdfPath);
                }
                $pdfFile = $req->file('pdf_file');
                $originalFileName = $pdfFile->getClientOriginalName();
                //store new PDF
                $pdfPath = $req->file('pdf_file')->storeAs('pdfs/house_and_lot', $originalFileName, 'public');
            }

            $houseAndLot->update([
                'address' => $validated['address'],
                'cost' => $validated['cost'],
                'pdf_path' => $pdfPath,
            ]);

            //append full URL for PDF before returning the response.
            if($houseAndLot->pdf_path) {
                $houseAndLot->pdf_url = Storage::url($houseAndLot->pdf_path);
            }
            else{
                $houseAndLot->pdf_url = null;
            }

            return response()->json([
                'success' => true,
                'message' => 'House & Lot updated successfully!',
                'data' => $houseAndLot
            ]);
        }
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error updating House & Lot: ' . $e->getMessage()
            ], 500);
        }
    }

    //SECTION -  company vehicle functions 
    public function getCompVehicle(){
        // return CompanyVehicle::all();
        $vehicles = CompanyVehicle::all();

        // Append full URL for PDF to each item before returning the response
        foreach($vehicles as $item){
            if($item->pdf_path){
                $item->vehicle_pdf_url = Storage::url($item->pdf_path);
            }
            else{
                $item->vehicle_pdf_url = null;
            }
        }
        return $vehicles;
    }

    public function insertCV(Request $req){
        try{
            $validated = $req->validate([
                'vehicle_name' => 'string|max:255',
                'cost' =>  ['nullable', 'numeric'],
                'pdf_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760'
            ]);

            $pdfPath = null;
            //check if a PDF file was uploaded.
            if ($req->hasFile('pdf_file')) {
                $pdfFile = $req->file('pdf_file');
                $originalFileName = $pdfFile->getClientOriginalName(); // Get the original file name

                // Store the PDF in 'storage/app/public/pdfs/vehicles' directory with its original name.
                // The 'public' disk is used, and the path returned is relative to that disk.
                $pdfPath = $pdfFile->storeAs('pdfs/vehicles', $originalFileName, 'public');
            }

            $insert = CompanyVehicle::create([
                'vehicle_name' => $validated['vehicle_name'],
                'cost' => $validated['cost'],
                'pdf_path' => $pdfPath
            ]);

            //append full URL for PDF before returning the response.
            if($insert->pdf_path) {
                $insert->pdf_url = Storage::url($insert->pdf_path);
            } 
            else{
                $insert->pdf_url = null;
            }

            return response()->json([
                'success' => true,
                'message' => 'New data inserted Successfully!',
                'data' => $insert
            ], 201);
        }
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function updateCV(Request $req, $id){
        // return $req;
        try{
            $vehicle = CompanyVehicle::findOrFail($id);

            $validated = $req->validate([
                'vehicle_name' => 'string|max:255',
                'cost' => ['nullable', 'numeric', 'min:0'],
                'pdf_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:104857600',
            ], [
                'pdf_file.max' => 'The PDF file must not exceed 10MB',
                'pdf_file.mimes' => 'Only JPG, JPEG, PNG, and PDF files are allowed',
                'cost.numeric' => 'The cost must be a number'
            ]);

            $pdfPath = $vehicle->pdf_path; //keep current path by default

            //handle PDF file update or removal
            if($req->hasFile('pdf_file')){
                //delete old PDF if exists
                if($pdfPath && Storage::disk('public')->exists($pdfPath)){
                    Storage::disk('public')->delete($pdfPath);
                }
                $pdfFile = $req->file('pdf_file');
                $originalFileName = $pdfFile->getClientOriginalName();
                //store new PDF
                $pdfPath = $req->file('pdf_file')->storeAs('pdfs/vehicles', $originalFileName, 'public');
            }

            $vehicle->update([
                'vehicle_name' => $validated['vehicle_name'],
                'cost' => $validated['cost'],
                'pdf_path' => $pdfPath,
            ]);

            //append full URL for PDF before returning the response.
            if($vehicle->pdf_path) {
                $vehicle->pdf_url = Storage::url($vehicle->pdf_path);
            }
            else{
                $vehicle->pdf_url = null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Vehicle updated successfully!',
                'data' => $vehicle
            ]);
        }
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error updating Vehicle: ' . $e->getMessage()
            ], 500);
        }
    }
}
