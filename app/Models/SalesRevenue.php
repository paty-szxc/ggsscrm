<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRevenue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_revenue';

    protected $fillable = [
        'date_of_survey',
        'location',
        'type_of_survey',
        'expenses',
        'receipt_no',
        'project_cost',
        'first_date_of_collection',
        'first_collection',
        'second_date_of_collection',
        'second_collection',
        'third_date_of_collection',
        'third_collection',
        'fourth_date_of_collection',
        'fourth_collection',
        'total',
        'receivable_bal',
        'withholding_tax',
        'fully_paid_date',
    ];
}
