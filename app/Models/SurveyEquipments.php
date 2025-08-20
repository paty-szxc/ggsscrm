<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyEquipments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'survey_equipments';

    protected $fillable = [
        'description',
        'price',
        'qty',
        'serial_no',
        'date_supplied',
        'status',
        'recos',
    ];
}
