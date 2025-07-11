<?php

namespace App\Http\Controllers;

use App\Models\CompanyVehicle;
use App\Models\HouseAndLot;
use App\Models\OfficeSupplies;
use Illuminate\Http\Request;

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
        return HouseAndLot::all();
    }

    public function insertHaL(Request $req){
        try{
            $validated = $req->validate([
                'address' => 'required|string|max:255',
                'cost' => [ 'nullable', 'numeric' ]
            ]);

            $insert = HouseAndLot::create([
                'address' => $validated['address'],
                'cost' => $validated['cost']
            ]);

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

    //* company vehicle functions *//
    public function getCompVehicle(){
        return CompanyVehicle::all();
    }

    public function insertCV(Request $req){
        try{
            $validated = $req->validate([
                'vehicle_name' => 'required|string|max:255',
                'cost' => [ 'nullable', 'numeric' ]
            ]);

            $insert = CompanyVehicle::create([
                'vehicle_name' => $validated['vehicle_name'],
                'cost' => $validated['cost']
            ]);

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
}
