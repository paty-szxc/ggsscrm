<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyGovernmentRelated extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'survey_government_related';

    protected $fillable = [
        'date_started',
        'client',
        'location',
        'type_of_plan_survey',
        'date_completed',
        'duration',
        'remarks',
    ];
}
