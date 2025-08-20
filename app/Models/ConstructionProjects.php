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
        'duration',
        'location',
        'particulars',
        'processed_by',
        'start_process',
        'end_process',
        'start_actual',
        'end_actual',
        'contact_person',
        'contact_no',
        'remarks',
    ];
}


