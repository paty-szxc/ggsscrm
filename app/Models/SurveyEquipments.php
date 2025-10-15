<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Define the inverse Many-to-Many relationship with SurveyEquipmentMovement.
     */
    public function movements(): BelongsToMany{
        return $this->belongsToMany(
            SurveyEquipmentMovement::class,
            'equipment_movement_pivot',    //the new dedicated pivot table name
            'survey_equipment_id',         //foreign key on pivot that references THIS model (Equipment)
            'equipment_movement_id'        //foreign key on pivot that references the Movement
        );
    }
}
