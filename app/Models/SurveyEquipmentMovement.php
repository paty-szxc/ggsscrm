<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SurveyEquipmentMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'survey_equipment_movements';

    protected $fillable = [
        // 'description',
        // 'equipment_id',
        'site',
        'handled_by',
        'incoming_date',
        'outgoing_date',
    ];

    /**
     * Define the Many-to-Many relationship with SurveyEquipments.
     * This method will allow you to access the descriptions.
     */
    public function equipments(): BelongsToMany{
        return $this->belongsToMany(
            SurveyEquipments::class, 
            'equipment_movement_pivot',    //the new dedicated pivot table name
            'equipment_movement_id',       //foreign key on pivot that references THIS model (Movement)
            'survey_equipment_id'          //foreign key on pivot that references the Survey Equipment
        );
    }
}