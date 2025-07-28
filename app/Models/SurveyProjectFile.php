<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyProjectFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_project_id', // Add this
        'original_name',
        'stored_name',
        'path',
        'mime_type',
        'size',
    ];

}
