<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenance extends Model
{
    use HasFactory, SoftDeletes;

        protected $table = 'vehicle_maintenance';

        protected $fillable = [
        'company_vehicle_id',
        'date',
        'particulars',
        'materials',
        'amount',
    ];

    public function vehicle(){
    return $this->belongsTo(CompanyVehicle::class, 'company_vehicle_id');
    }
}
