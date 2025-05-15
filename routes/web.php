<?php

use App\Http\Controllers\SalesRevenueController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return Inertia::render('Home'); // This should correspond to a Home.vue component in your Pages directory
});

Route::inertia('/dashboard', 'Dashboard');
Route::inertia('/survey_monitoring', 'SurveyMonitoring');
Route::inertia('/sales_and_revenue', 'SalesRevenue');


Route::get('get_survey_data', [SurveyController::class, 'index']);
Route::post('import_survey_data', [SurveyController::class, 'import']);
Route::post('insert_survey_data', [SurveyController::class, 'insert']);
Route::post('update_survey_data', [SurveyController::class, 'update']);
Route::get('get_sales_revenue_data', [SalesRevenueController::class, 'index']);
Route::post('import_sales_revenue_data', [SalesRevenueController::class, 'import']);