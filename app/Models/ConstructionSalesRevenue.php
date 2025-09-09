<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionSalesRevenue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'construction_sales_revenue';

    protected $fillable = [
        'date',
        'client_name_address',
        'particulars',
        'status_of_vat',
        'receipt_no',
        'project_cost',
        'amount_gross_of_vat',
        'net_of_vat',
        'vat',
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
        'others',
        'remarks',
        'fully_paid_date',
        'withholding_tax'
    ];

    protected static function boot(){
        parent::boot();
        static::saving(function ($model) {
            //calculate total collections
            $model->total = 
                ($model->first_collection ?? 0) +
                ($model->second_collection ?? 0) +
                ($model->third_collection ?? 0) +
                ($model->fourth_collection ?? 0);

            //calculate receivable balance
            $model->receivable_bal = ($model->project_cost ?? 0) - $model->total;

            // Only set fully_paid_date if the row has real data
            $hasRealData = !empty($model->client_name_address) || !empty($model->particulars) || !empty($model->project_cost);

            if($hasRealData && $model->receivable_bal <= 0){
                $model->fully_paid_date = $model->fully_paid_date ?? now();
            } 
            else{
                $model->fully_paid_date = null;
            }
        });
    }
}
