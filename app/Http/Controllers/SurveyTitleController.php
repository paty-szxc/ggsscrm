<?php

namespace App\Http\Controllers;

use App\Models\SurveyTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SurveyTitleController extends Controller
{
    // public function index(){
    //     $titles = SurveyTitle::all();
    //     foreach($titles as $item){
    //         //decode the JSON string back into a PHP array
    //         $lotPlans = json_decode($item->lot_plan, true); 
    //         $urls = [];
    //         if(is_array($lotPlans)){
    //             foreach($lotPlans as $path){
    //                 //ensure the file exists before generating a URL to prevent errors
    //                 if(Storage::disk('public')->exists($path)){
    //                     $urls[] = Storage::url($path);
    //                 }
    //             }
    //         }
    //         $item->plan_attachment = $urls; //assign the array of URLs
    //     }
    //     return $titles;
    // }
    public function index(){
        $titles = SurveyTitle::all();
        foreach($titles as $item){
            $lotPlans = json_decode($item->lot_plan, true); 
            $urls = [];
            if(is_array($lotPlans)){
                foreach($lotPlans as $path){
                    if(Storage::disk('public')->exists($path)){
                        $urls[] = Storage::url($path);
                    }
                }
            }
            $item->plan_attachment = $urls;
            //NOTE: lot_plan_original_name is now either a string or a JSON array of names
            //which will be decoded and used by the front-end getFileName function.
        }
        return $titles;
    }

    public function insert(Request $req){
        try{
            $validated = $req->validate([
                'date_of_survey' => 'required|date',
                'client' => 'nullable|string|max:255',
                'type_of_survey' => 'nullable|string|max:255',
                'lot_plan_files' => 'required|array',
                'lot_plan_files.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,dwg|max:104857600', //100 MB max per file
            ]);

            $filePaths = [];
            $originalNames = [];

            if($req->hasFile('lot_plan_files')){
                foreach($req->file('lot_plan_files') as $file){
                    $origAttFileName = $file->getClientOriginalName();
                    $filePaths[] = $file->store('/titles', 'public');
                    $originalNames[] = $origAttFileName; //save original name
                }
            }

            $insert = SurveyTitle::create([
                'date_of_survey' => $validated['date_of_survey'],
                'client' => $validated['client'],
                'type_of_survey' => $validated['type_of_survey'],
                'lot_plan' => json_encode($filePaths),
                //save all original names as a JSON array
                'lot_plan_original_name' => json_encode($originalNames) 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'New data inserted successfully!',
                'data' => $insert
            ], 201);
        }
        catch(ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the survey record.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $req, $id){
        try{
            $surveyTitle = SurveyTitle::findOrFail($id);
            $validated = $req->validate([
                'date_of_survey' => 'required|date',
                'client' => 'nullable|string|max:255',
                'type_of_survey' => 'nullable|string|max:255',
                'lot_plan_files' => 'nullable|array',
                'lot_plan_files.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,dwg|max:104857600',
            ]);

            //decode existing attachments and original names
            $currentFilePaths = json_decode($surveyTitle->lot_plan, true) ?? [];
            //decode existing original names (can be string or array)
            $currentOriginalNames = json_decode($surveyTitle->lot_plan_original_name, true) ?? [];
            //if it's a string (legacy data), wrap it in an array to ensure consistency
            if(!is_array($currentOriginalNames) && !empty($surveyTitle->lot_plan_original_name)){
                $currentOriginalNames = [$surveyTitle->lot_plan_original_name];
            }
            elseif(!is_array($currentOriginalNames)){
                $currentOriginalNames = [];
            }
            
            $publicPrefix = '/storage';

            //handle file deletions
            if($req->filled('files_to_delete')){
                $filesToDelete = json_decode($req->input('files_to_delete'), true);
                if(is_array($filesToDelete)){
                    foreach($filesToDelete as $fileUrl){
                        $filePath = str_replace($publicPrefix . '/', '', $fileUrl);
                        
                        //find the key/index of the file path to be deleted
                        if(($key = array_search($filePath, $currentFilePaths)) !== false){
                            //remove file from storage
                            if(Storage::disk('public')->exists($filePath)){
                                Storage::disk('public')->delete($filePath);
                            }

                            //remove the path from the current file list
                            unset($currentFilePaths[$key]);
                            //remove the corresponding original name from the list
                            if(isset($currentOriginalNames[$key])){
                                unset($currentOriginalNames[$key]);
                            }
                        }
                    }
                }
            }

            //handle new file uploads
            if($req->hasFile('lot_plan_files')){
                foreach($req->file('lot_plan_files') as $file){
                    $origAttFileName = $file->getClientOriginalName(); // <-- This must be correct
                    $currentFilePaths[] = $file->store('/titles', 'public');
                    $currentOriginalNames[] = $origAttFileName; 
                }
            }

            //update the model with the new data and file list
            $surveyTitle->date_of_survey = $validated['date_of_survey'];
            $surveyTitle->client = $validated['client'];
            $surveyTitle->type_of_survey = $validated['type_of_survey'];
            
            //re-index and encode the updated lists
            $surveyTitle->lot_plan = json_encode(array_values($currentFilePaths));
            //save the updated array of original names
            $surveyTitle->lot_plan_original_name = json_encode(array_values($currentOriginalNames));
            
            $surveyTitle->save();

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully!',
                'data' => $surveyTitle
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the record.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
