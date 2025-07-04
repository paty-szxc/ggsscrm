<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionProjects extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'construction_projects';

    protected $fillable = [
        'user_id',
        'date_started',
        'date_completed',
        'client',
        'location',
        'type_of_plan_survey',
        'duration',
        'remarks',
    ];
}


