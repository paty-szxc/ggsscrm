<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConstructionExpensesController;
use App\Http\Controllers\ConstructionProjectsController;
use App\Http\Controllers\ConstructionQuotationController;
use App\Http\Controllers\ConstructionSalesRevenueController;
use App\Http\Controllers\OfficeSuppliesController;
use App\Http\Controllers\QuotationsController;
use App\Http\Controllers\SalesRevenueController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\RelocationSurveyController;
use App\Http\Controllers\SurveyEquipmentsController;
use App\Http\Controllers\SurveyGovernmentRelatedController;
use App\Http\Controllers\SurveyTitleController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Models\SurveyEquipments;
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

    //SECTION -  GGSS routes
    Route::group(['headerTitle' => 'Geopete Geodetic Surveying Services'], function() {
        Route::inertia('/dashboard', 'GGSS/SurveyDashboard')->middleware('permission:dashboard_access');
        Route::inertia('/survey_monitoring', 'GGSS/SurveyMonitoring')->middleware('permission:survey_monitoring_access');
        Route::inertia('/sales_and_revenue', 'GGSS/SurveySalesRevenue')->middleware('permission:sales_revenue_access');
        Route::inertia('/expenses', 'GGSS/SurveyExpenses')->middleware('permission:expenses_access');
        Route::inertia('/office_equipment', 'GGSS/OfficeEquipment')->middleware('permission:office_equipment_access');
        Route::inertia('/company_assets', 'GGSS/CompanyAsset')->middleware('permission:company_assets_access');
        Route::inertia('/survey_quotation', 'GGSS/SurveyQuotations')->middleware('permission:quotation_access');
        Route::inertia('/survey_equipments', 'GGSS/SurveyEquipments')->middleware('permission:survey_equipment_access');
        Route::inertia('/govt_external', 'GGSS/SurveyGovtAffairs')->middleware('permission:govt_external_access');
        Route::inertia('/titles', 'GGSS/SurveyTitles')->middleware('permission:titles_access');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout');

    //SURVEY PROJECTS
    Route::get('get_survey_data', [SurveyController::class, 'index'])->middleware('permission:survey_monitoring_access');
    Route::get('get_current_user_for_survey', [SurveyController::class, 'getCurrentUser']);
    Route::post('import_survey_data', [SurveyController::class, 'import'])->middleware('permission:survey_monitoring_access');
    Route::post('insert_survey_data', [SurveyController::class, 'insert'])->middleware('permission:survey_monitoring_access');
    Route::post('update_survey_data', [SurveyController::class, 'update'])->middleware('permission:survey_monitoring_access');
    Route::get('yearly_chart_data', [SurveyController::class, 'getYearlyChartData'])->middleware('permission:dashboard_access');

    //SALES & REVENUE
    Route::get('get_sales_revenue_data', [SalesRevenueController::class, 'index'])->middleware('permission:sales_revenue_access');
    Route::post('import_sales_revenue_data', [SalesRevenueController::class, 'import'])->middleware('permission:sales_revenue_access');
    Route::post('insert_sales_revenue_data', [SalesRevenueController::class, 'insert'])->middleware('permission:sales_revenue_access');
    Route::post('update_sales_revenue_data', [SalesRevenueController::class, 'update'])->middleware('permission:sales_revenue_access');
    Route::get('monthly_totals', [SalesRevenueController::class, 'monthlyCosts'])->middleware('permission:sales_revenue_access');
    Route::get('yearly_sales', [SalesRevenueController::class, 'yearlySales'])->middleware('permission:sales_revenue_access');

    //EXPENSES DATA
    Route::get('get_vouchers', [VoucherController::class, 'index'])->middleware('permission:expenses_access');
    Route::post('import_vouchers_data', [VoucherController::class, 'import'])->middleware('permission:expenses_access');
    Route::post('insert_vouchers_data', [VoucherController::class, 'insert'])->middleware('permission:expenses_access');
    Route::post('update_vouchers_data', [VoucherController::class, 'update'])->middleware('permission:expenses_access');
    Route::get('monthly_expenses', [VoucherController::class, 'monthlyExpenses'])->middleware('permission:expenses_access');
    Route::get('yearly_expenses', [VoucherController::class, 'yearlyExpenses'])->middleware('permission:expenses_access');

    //QUOTATIONS
    Route::get('/get_quotations', [QuotationsController::class, 'index'])->middleware('permission:quotation_access');
    Route::post('insert_survey_quotation', [QuotationsController::class, 'insert'])->middleware('permission:quotation_access');
    Route::put('update_survey_quotation/{id}', [QuotationsController::class, 'update'])->middleware('permission:quotation_access');

    //OFFICE SUPPLIES
    Route::get('get_office_supplies', [OfficeSuppliesController::class, 'index'])->middleware('permission:office_equipment_access');
    Route::post('insert_office_supplies', [OfficeSuppliesController::class, 'insert'])->middleware('permission:office_equipment_access');
    Route::post('update_office_supplies', [OfficeSuppliesController::class, 'update'])->middleware('permission:office_equipment_access');

    //SURVEY EQUIPMENTS
    Route::get('get_survey_equipments', [SurveyEquipmentsController::class, 'index'])->middleware('permission:survey_equipment_access');
    Route::post('insert_survey_equipment', [SurveyEquipmentsController::class, 'insert'])->middleware('permission:survey_equipment_access');
    Route::post('update_survey_equipment', [SurveyEquipmentsController::class, 'update'])->middleware('permission:survey_equipment_access');
    Route::get('get_incoming_outgoing_equipments', [SurveyEquipmentsController::class, 'getIO'])->middleware('permission:survey_equipment_access');
    Route::post('insert_incoming_outgoing_equipment', [SurveyEquipmentsController::class, 'insertIO'])->middleware('permission:survey_equipment_access');
    Route::post('update_incoming_outgoing_equipment', [SurveyEquipmentsController::class, 'updateIO'])->middleware('permission:survey_equipment_access');

    //COMPANY ASSET
    Route::get('get_house_and_lot', [OfficeSuppliesController::class, 'getHaL'])->middleware('permission:company_assets_access');
    Route::get('get_personal_house_and_lot', [OfficeSuppliesController::class, 'getPersonalHaL'])->middleware('permission:company_assets_access');
    Route::post('insert_house_and_lot', [OfficeSuppliesController::class, 'insertHaL'])->middleware('permission:company_assets_access');
    Route::put('update_house_and_lot/{id}', [OfficeSuppliesController::class, 'updateHaL'])->middleware('permission:company_assets_access');
    Route::get('get_company_vehicle', [OfficeSuppliesController::class, 'getCompVehicle'])->middleware('permission:company_assets_access');
    Route::get('get_personal_vehicle', [OfficeSuppliesController::class, 'getPersonalVehicle'])->middleware('permission:company_assets_access');
    Route::post('insert_company_vehicle', [OfficeSuppliesController::class, 'insertCV'])->middleware('permission:company_assets_access');
    Route::put('update_company_vehicle/{id}', [OfficeSuppliesController::class, 'updateCV'])->middleware('permission:company_assets_access');
    Route::get('get_maintenance_table_data', [VehicleMaintenanceController::class, 'index'])->middleware('permission:company_assets_access');
    Route::get('get_maintenance_record/{id}', [VehicleMaintenanceController::class, 'show'])->middleware('permission:company_assets_access');
    Route::post('insert_data_in_maintenance_table', [VehicleMaintenanceController::class, 'insert'])->middleware('permission:company_assets_access');
    Route::put('update_data_in_maintenance_table/{id}', [VehicleMaintenanceController::class, 'update'])->middleware('permission:company_assets_access');


    //GOVT RELATED
    Route::get('get_survey_govt_related_data', [SurveyGovernmentRelatedController::class, 'index'])->middleware('permission:govt_external_access');
    Route::get('get_current_user_for_govt', [SurveyGovernmentRelatedController::class, 'getCurrentUser']);
    Route::post('insert_survey_govt_related_data', [SurveyGovernmentRelatedController::class, 'insert'])->middleware('permission:govt_external_access');
    Route::post('update_survey_govt_related_data', [SurveyGovernmentRelatedController::class, 'update'])->middleware('permission:govt_external_access');

    //TITLES
    Route::get('get_survey_title_data', [SurveyTitleController::class, 'index'])->middleware('permission:titles_access');
    Route::post('insert_survey_title', [SurveyTitleController::class, 'insert'])->middleware('permission:titles_access');
    Route::put('update_survey_title/{id}', [SurveyTitleController::class, 'update'])->middleware('permission:titles_access');

    //SECTION -  GCO routes
    Route::group(['headerTitle' => 'Geopete Construction'], function() {
        // The construction monitoring route is now handled separately with the 'gco_access' permission
        Route::inertia('/construction_dashboard', 'GCO/ConstructionDashboard')->middleware('role:Admin');
        Route::inertia('/construction_revenue', 'GCO/ConstructionSales')->middleware('role:Admin');
        Route::inertia('/construction_expenses', 'GCO/ConstructionExpenses')->middleware('role:Admin');
    });

    //dedicated route for Construction Monitoring with 'gco_access' permission
    Route::inertia('/construction_monitoring', 'GCO/ConstructionMonitoring')->middleware('permission:gco_access');
    Route::inertia('/construction_quotation', 'GCO/ConstructionQuotation')->middleware('permission:gco_access');

    //CONSTRUCTION PROJECT
    //these routes will now also require 'gco_access' to match the new permission structure
    Route::get('get_construction_project_data', [ConstructionProjectsController::class, 'index'])->middleware('permission:gco_access');
    Route::get('get_current_user', [ConstructionProjectsController::class, 'getCurrentUser']);
    Route::post('import_construction_projects_data', [ConstructionProjectsController::class, 'import'])->middleware('permission:gco_access');
    Route::post('insert_construction_project_data', [ConstructionProjectsController::class, 'insert'])->middleware('permission:gco_access');
    Route::post('update_construction_project_data', [ConstructionProjectsController::class, 'update'])->middleware('permission:gco_access');
    
    //CONSTRUCTION SALES & REVENUE
    Route::get('get_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'index'])->middleware('permission:gco_access');
    Route::post('import_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'import'])->middleware('permission:gco_access');
    Route::post('insert_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'insert'])->middleware('permission:gco_access');
    Route::post('update_construction_sales_revenue', [ConstructionSalesRevenueController::class, 'update'])->middleware('permission:gco_access');
    Route::get('construction_monthly_totals', [ConstructionSalesRevenueController::class, 'monthlyCosts'])->middleware('permission:gco_access');
    Route::get('construction_yearly_sales', [ConstructionSalesRevenueController::class, 'yearlySales'])->middleware('permission:gco_access');
    
    //CONSTRUCTION QUOTATIONS
    Route::get('get_construction_quotations', [ConstructionQuotationController::class, 'index'])->middleware('permission:gco_access');
    Route::post('insert_construction_quotation', [ConstructionQuotationController::class, 'insert'])->middleware('permission:gco_access');
    Route::put('update_construction_quotation/{id}', [ConstructionQuotationController::class, 'update'])->middleware('permission:gco_access');

    //CONSTRUCTION EXPENSES
    Route::get('get_construction_vouchers', [ConstructionExpensesController::class, 'index'])->middleware('permission:gco_access');
    Route::post('import_construction_vouchers_data', [ConstructionExpensesController::class, 'import'])->middleware('permission:gco_access');
    Route::post('insert_construction_vouchers_data', [ConstructionExpensesController::class, 'insert'])->middleware('permission:gco_access');
    Route::post('update_construction_vouchers_data', [ConstructionExpensesController::class, 'update'])->middleware('permission:gco_access');
    Route::get('construction_monthly_expenses', [ConstructionExpensesController::class, 'monthlyExpenses'])->middleware('permission:gco_access');
    Route::get('construction_yearly_expenses', [ConstructionExpensesController::class, 'yearlyExpenses'])->middleware('permission:gco_access');
});
