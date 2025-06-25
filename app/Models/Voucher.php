<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vouchers';

    protected $fillable = [
        'voucher_no',
        'date',
        'suppliers_name',
        'address_brgy_city',
        'status_of_vat',
        'tin',
        'particulars',
        'employee_salary',
        'employee_benefits',
        'meals_office_survey',
        'dog_food',
        'construction_survey_supplies',
        'repairs_maintenance',
        'office_supplies',
        'gasoline_oil',
        'utilities',
        'parking_fee',
        'toll_fee',
        'permits_certification_tax',
        'transportation',
        'budget',
        'representation_expense_personal',
        'others_staff_personal',
        'amount_gross_of_vat',
        'net_of_vat',
        'vat'
    ];

    protected static function booted(){
        static::saving(function($model){
            $numericFields = [
                'employee_salary',
                'employee_benefits',
                'meals_office_survey',
                'dog_food',
                'construction_survey_supplies',
                'repairs_maintenance',
                'office_supplies',
                'gasoline_oil',
                'utilities',
                'parking_fee',
                'toll_fee',
                'permits_certification_tax',
                'transportation',
                'budget',
                'representation_expense_personal',
                'others_staff_personal',
                'amount_gross_of_vat',
                'net_of_vat',
                'vat'
            ];

            foreach($numericFields as $field){
                if(isset($model->$field)){
                    $model->$field = is_numeric($model->$field) 
                        ? $model->$field 
                        : floatval(str_replace(',', '', $model->$field));
                }
            }
        });
    }

    public function getDateAttribute($value){
        return $value ? \Carbon\Carbon::parse($value)->format('M j, Y') : null;
    }


}
