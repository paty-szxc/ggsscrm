<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Imports\SurveysImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SurveyProjectFile;

class SurveyController extends Controller
{
    public function index(Request $req){
        $user = auth()->user();
        $showAll = $req->input('show_all', 1);
        if(($user->id == 1 || $user->id == 3) && $showAll){
            return Survey::orderBy('date_started', 'desc')->with('files')->get();
        }
        else{
            return Survey::where('user_id', $user->id)
            ->orderBy('date_started', 'desc')
            ->with('files')
            ->get();
        }
    }

    public function getCurrentUser(){
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
        ]); 
    }

    public function import(Request $req) {
        $req->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', // Added max file size (10MB)
        ]);

        try{
            Log::info('Starting survey import');
            
            $file = $req->file('file');
            Log::info('Importing file: ' . $file->getClientOriginalName());
            
            Excel::import(new SurveysImport(), $file);
            Log::info('Survey import completed successfully');
            
            return redirect()->back()
                ->with('success', 'Surveys imported successfully!');
                
        } 
        catch(\Exception $e){
            Log::error('Survey import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Import failed. ' . 
                ($e instanceof \Maatwebsite\Excel\Validators\ValidationException ?
                    'Please check your file format and data.' :
                    $e->getMessage());
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function insert(Request $req){
        // return $req;
        $userId = Auth::id();
        $insert = Survey::create([
            'user_id' => $userId,
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'survey_details' => $req->to_update['survey_details'],
            'area' => $req->to_update['area'] ?? null, 
            'processed_by' => $req->to_update['processed_by'] ?? null, 
            'survey' => isset($req->to_update['survey']) ? $req->to_update['survey'] : 0, 
            'data_process' => $req->to_update['data_process'] ?? null,
            'plans' => $req->to_update['plans'] ?? null,
            'date_approved' => $req->to_update['date_approved'] ?? null,
            'date_completed' => $req->to_update['date_completed'] ?? null,
            'remarks' => $req->to_update['remarks'] ?? null,
            'contact_person' => $req->to_update['contact_person'] ?? null,
            'contact_no' => $req->to_update['contact_no'] ?? null, 
            'thru' => $req->to_update['thru'] ?? null,
        ]);
        return response()->json([
            'message' => 'Survey data inserted successfully!',
            'data' => $insert,
        ], 201);
    }

    public function update(Request $req){
        // return $req;

        // return $req->to_update['remarks'];
        $survey = Survey::find($req->to_update['id']);

        // return $survey;

        $survey->update([
            'date_started' => $req->to_update['date_started'],
            'location' => $req->to_update['location'],
            'survey_details' => $req->to_update['survey_details'],
            'area' => $req->to_update['area'],
            'processed_by' => $req->to_update['processed_by'],
            'survey' => isset($req->to_update['survey']) ? $req->to_update['survey'] : 0,
            'data_process' => $req->to_update['data_process'],
            'plans' => $req->to_update['plans'],
            'date_approved' => $req->to_update['date_approved'],
            'date_completed' => $req->to_update['date_completed'],
            'remarks' => $req->to_update['remarks'],
            'contact_person' => $req->to_update['contact_person'],
            'contact_no' => $req->to_update['contact_no'],
            'thru' => $req->to_update['thru'],
        ]);
    }
    

    public function upload(Request $request){
        // return $request; 
        // return $request->hasFile('files');
        // 1. Validate the incoming request, including the survey_project_id
        $request->validate([
            // 'survey_project_id' => 'required|exists:survey_projects,id', // Ensure it exists in the projects table
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt|max:104857600',
        ], [
            // 'survey_project_id.exists' => 'The selected survey project does not exist.',
            'files.*.mimes' => 'The :attribute must be a file of type: :values.',
            'files.*.max' => 'The :attribute may not be greater than :max kilobytes.',
        ]);

        //make dynamic, if insert get last id if edit get the current id
        $edit_survey_id = $request->input('survey_project_id');
        $lastId = Survey::max('id') + 1;
        $surveyProjectId = $edit_survey_id ? $edit_survey_id : $lastId;
        // return $surveyProjectId;
        $uploadedFileDetails = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();
                $storedName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('survey_files', $storedName, 'public');

                //save file metadata to the database, linking it to the survey project
                $fileEntry = SurveyProjectFile::create([
                    'survey_project_id' => $surveyProjectId, // Assign the received ID
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'mime_type' => $mimeType,
                    'size' => $fileSize,
                ]);

                $uploadedFileDetails[] = [
                    'id' => $fileEntry->id,
                    'original_name' => $originalName,
                    'path' => Storage::url($path)
                ];
            }
        }
        return response()->json([
            'message' => 'Files uploaded successfully!',
            'uploaded_files' => $uploadedFileDetails,
        ], 200);
    }

    // public function remove($id) // Accept route parameter directly
    // {
    //     $file = SurveyProjectFile::find($id); // Find by route parameter

    //     if (!$file) {
    //         return response()->json(['message' => 'File not found.'], 404);
    //     }

    //     try {
    //         if (Storage::disk('public')->exists($file->path)) {
    //             Storage::disk('public')->delete($file->path);
    //         }

    //         $file->delete();
    //         return response()->json(['message' => 'File deleted successfully.']);

    //     } catch (\Exception $e) {
    //         Log::error('Error deleting file: ' . $e->getMessage());
    //         return response()->json(['message' => 'Failed to delete file.'], 500);
    //     }
    // }

    // public function show($id)
    // {
    //     // Fetch a single survey project by ID and eager load its associated files
    //     $survey_project = SurveyProjectFile::with('files')->find($id);

    //     if (!$survey_project) {
    //         return response()->json(['message' => 'Survey project not found'], 404);
    //     }

    //     return response()->json($survey_project);
    // }
}
