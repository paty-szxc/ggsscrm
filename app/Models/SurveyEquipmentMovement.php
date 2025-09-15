<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyEquipmentMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'survey_equipment_movements';

    protected $fillable = [
        'description',
        'site',
        'handled_by',
        'incoming_date',
        'outgoing_date',
    ];
}