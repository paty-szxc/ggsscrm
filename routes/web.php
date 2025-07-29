<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConstructionProjectsController;
use App\Http\Controllers\ConstructionSalesRevenueController;
use App\Http\Controllers\OfficeSuppliesController;
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

    Route::inertia('/ggss', 'GGSS/Ggss');
    Route::inertia('/gco', 'GCO/Gco');

    //GGSS routes
    Route::group(['headerTitle' => 'Geopete Geodetic Surveying Services'], function() {
        Route::inertia('/dashboard', 'GGSS/SurveyDashboard');
        Route::inertia('/survey_monitoring', 'GGSS/SurveyMonitoring');
        Route::inertia('/sales_and_revenue', 'GGSS/SurveySalesRevenue');
        Route::inertia('/expenses', 'GGSS/SurveyExpenses');
        Route::inertia('/office_equipment', 'GGSS/OfficeEquipment');
        Route::inertia('/company_assets', 'GGSS/CompanyAsset');
        Route::inertia('/survey_quotation', 'GGSS/SurveyQuotations');
    });

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
    Route::get('yearly_sales', [SalesRevenueController::class, 'yearlySales']);

    //EXPENSES DATA
    Route::get('get_vouchers', [VoucherController::class, 'index']);
    Route::post('import_vouchers_data', [VoucherController::class, 'import']);
    Route::post('insert_vouchers_data', [VoucherController::class, 'insert']);
    Route::post('update_vouchers_data', [VoucherController::class, 'update']);
    Route::get('monthly_expenses', [VoucherController::class, 'monthlyExpenses']);
    Route::get('yearly_expenses', [VoucherController::class, 'yearlyExpenses']);

    //QUOTATIONS
    Route::get('/generate_relo_survey_pdf', [RelocationSurveyController::class, 'generatePdf']);

    Route::get('get_office_supplies', [OfficeSuppliesController::class, 'index']);
    Route::post('insert_office_supplies', [OfficeSuppliesController::class, 'insert']);
    Route::post('update_office_supplies', [OfficeSuppliesController::class, 'update']);

    Route::get('get_house_and_lot', [OfficeSuppliesController::class, 'getHaL']);
    Route::post('insert_house_and_lot', [OfficeSuppliesController::class, 'insertHaL']);
    Route::put('update_house_and_lot/{id}', [OfficeSuppliesController::class, 'updateHaL']);
    Route::get('get_company_vehicle', [OfficeSuppliesController::class, 'getCompVehicle']);
    Route::post('insert_company_vehicle', [OfficeSuppliesController::class, 'insertCV']);
    Route::put('update_company_vehicle/{id}', [OfficeSuppliesController::class, 'updateCV']);


    //GCO routes
    Route::group(['headerTitle' => 'Geopete Construction'], function() {
        Route::inertia('/construction_dashboard', 'GCO/ConstructionDashboard');
        Route::inertia('/construction_monitoring', 'GCO/ConstructionMonitoring');
        Route::inertia('/construction_revenue', 'GCO/ConstructionSales');
        Route::inertia('/construction_expenses', 'GCO/ConstructionExpenses');
    });

    Route::get('get_construction_project_data', [ConstructionProjectsController::class, 'index']);
    Route::get('get_current_user', [ConstructionProjectsController::class, 'getCurrentUser']);
    Route::post('import_construction_projects_data', [ConstructionProjectsController::class, 'import']);
    Route::post('insert_construction_project_data', [ConstructionProjectsController::class, 'insert']);
    Route::post('update_construction_project_data', [ConstructionProjectsController::class, 'update']);
    Route::get('get_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'index']);
    Route::post('import_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'import']);
    Route::post('insert_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'insert']);
    Route::post('update_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'update']);
    Route::get('construction_monthly_totals', [ConstructionSalesRevenueController::class, 'monthlyCosts']);

    Route::get('construction_monthly_expenses', [ConstructionSalesRevenueController::class, 'monthlyExpenses']);
});  