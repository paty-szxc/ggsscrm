<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'survey_titles';

    protected $fillable = [
        'date_of_survey',
        'client',
        'type_of_survey',
        'lot_plan',
        'lot_plan_original_name'
    ];
}
