<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;

class EmployeesController extends Controller
{
    public function getEmpSalary(){
        $salary = Employees::get();
        return $salary;
    }
}
