<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/files/upload', [SurveyController::class, 'upload']);
Route::post('/files/delete/{id}', [SurveyController::class, 'remove']);
Route::get('/files/survey/{survey_project_id}', [SurveyController::class, 'getSurveyFiles']);
