<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesExpensesController extends Controller
{
    public function getSalesExpensesSummary(){
        $currentYear = date('Y');
        $summary = DB::table('vouchers')
            ;
    }
}
