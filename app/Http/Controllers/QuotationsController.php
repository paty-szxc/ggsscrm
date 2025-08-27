<?php

namespace App\Http\Controllers;

use App\Models\Quotations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuotationsController extends Controller
{
    public function index(){
        $quotation = Quotations::all();

        foreach($quotation as $item){ //append full URL for PDF to each item before returning the response
            if($item->attachment){ //attachment
                $item->attachment_url = Storage::url($item->attachment);
            }
            else{
                $item->attachment_url = null;
            }

            if($item->revised_attachment){ //revised attachment
                $item->revised_attachment_url = Storage::url($item->revised_attachment);
            }
            else{
                $item->revised_attachment_url = null;
            }
        }
        return $quotation;
    }

    public function insert(Request $req){
        try{
            $validated = $req->validate([
                'client' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'attachment_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760',
                'revised_attachment_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760'
            ]);

            $attFilePath = null;
            if($req->hasFile('attachment_file')){
                $attFile = $req->file('attachment_file');
                $origAttFileName = $attFile->getClientOriginalName();
                $attFilePath = $attFile->storeAs('pdfs/quotations', $origAttFileName, 'public');
            }

            $revAttFilePath = null;
            if($req->hasFile('revised_attachment_file')){
                $revAttFile = $req->file('revised_attachment_file');
                $origRevAttFileName = $revAttFile->getClientOriginalName();
                $revAttFilePath = $revAttFile->storeAs('pdfs/revised_quotations', $origRevAttFileName, 'public');
            }

            $insert = Quotations::create([
                'client' => $validated['client'],
                'location' => $validated['location'],
                'date' => $validated['date'],
                'attachment' => $attFilePath,
                'revised_attachment' => $revAttFilePath
            ]);

            $insert->attachment_url = $attFilePath ? Storage::url($attFilePath) : null;
            $insert->revised_attachment_url = $revAttFilePath ? Storage::url($revAttFilePath) : null;

            return response()->json([
                'success' => true,
                'message' => 'New data inserted successfully!',
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

    public function update(Request $req, $id){
        try{
            $quotation = Quotations::findOrfail($id);

            $validated = $req->validate([
                'client' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'attachment_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760',
                'revised_attachment_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:10485760'
            ]);

            //handle attachment file update
            if($req->hasFile('attachment_file')){
                //delete old file if it exists
                if($quotation->attachment){
                    Storage::disk('public')->delete($quotation->attachment);
                }
                $attFile = $req->file('attachment_file');
                $origAttFileName = $attFile->getClientOriginalName();
                $attFilePath = $attFile->storeAs('pdfs/quotations', $origAttFileName, 'public');
                $quotation->attachment = $attFilePath;
            }

            //handle revised attachment file update
            if($req->hasFile('revised_attachment_file')){
                //delete old file if it exists
                if($quotation->revised_attachment){
                    Storage::disk('public')->delete($quotation->revised_attachment);
                }
                
                $revAttFile = $req->file('revised_attachment_file');
                $origRevAttFileName = $revAttFile->getClientOriginalName();
                $revAttFilePath = $revAttFile->storeAs('pdfs/revised_quotations', $origRevAttFileName, 'public');
                $quotation->revised_attachment = $revAttFilePath;
            }

            $quotation->client = $validated['client'] ?? $quotation->client;
            $quotation->location = $validated['location'] ?? $quotation->location;
            $quotation->date = $validated['date'] ?? $quotation->date;
            $quotation->save();

            $quotation->attachment_url = $quotation->attachment ? Storage::url($quotation->attachment) : null;
            $quotation->revised_attachment_url = $quotation->revised_attachment ? Storage::url($quotation->revised_attachment) : null;

            return response()->json([
                'success' => true,
                'message' => 'Quotation updated successfully!',
                'data' => $quotation
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
                'message' => 'Error updating quotation: ' . $e->getMessage()
            ], 500);
        }
    }
}
