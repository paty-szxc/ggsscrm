<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SalesRevenueController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\RelocationSurveyController;
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
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
// Route::post('/logout', [LoginController::class, 'destroy'])
//     ->name('logout');
// Route::get('/', function () {
//     return Inertia::render('Home'); // This should correspond to a Home.vue component in your Pages directory
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home'); // This should correspond to a Home.vue component in your Pages directory
    });
    
    Route::inertia('/dashboard', 'Dashboard');
    Route::inertia('/survey_monitoring', 'SurveyMonitoring');
    Route::inertia('/sales_and_revenue', 'SalesRevenue');
    Route::inertia('/expenses', 'Expenses');

    Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout');

    Route::get('get_survey_data', [SurveyController::class, 'index']);
    Route::get('get_current_user', [SurveyController::class, 'getCurrentUser']);
    Route::post('import_survey_data', [SurveyController::class, 'import']);
    Route::post('insert_survey_data', [SurveyController::class, 'insert']);
    Route::post('update_survey_data', [SurveyController::class, 'update']);
    Route::get('get_sales_revenue_data', [SalesRevenueController::class, 'index']);
    Route::post('import_sales_revenue_data', [SalesRevenueController::class, 'import']);
    Route::post('insert_sales_revenue_data', [SalesRevenueController::class, 'insert']);
    Route::post('update_sales_revenue_data', [SalesRevenueController::class, 'update']);
    Route::get('monthly_totals', [SalesRevenueController::class, 'monthlyCosts']);

    // EXPENSES DATA
    Route::get('get_vouchers', [VoucherController::class, 'index']);
    Route::post('import_vouchers_data', [VoucherController::class, 'import']);
    Route::get('monthly_expenses', [VoucherController::class, 'monthlyExpenses']);

    Route::get('/generate_relo_survey_pdf', [RelocationSurveyController::class, 'generatePdf']);
});