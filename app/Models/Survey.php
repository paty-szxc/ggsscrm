<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SurveyProjectFile;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'survey_projects';

    protected $fillable = [
        'user_id',
        'date_started',
        'date_completed',
        'location',
        'survey_details',
        'area',
        'processed_by',
        'survey',
        'data_process',
        'plans',
        'contact_person',
        'contact_no',
        'date_approved',
        'date_delivered',
        'remarks',
        'thru'
    ];

    public function files()
    {
        return $this->hasMany(SurveyProjectFile::class, 'survey_project_id', 'id'); // 'survey_project_id' is the foreign key in the files table
    }
}


