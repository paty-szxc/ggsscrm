<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_vehicle';

    protected $fillable = [
        'vehicle_name',
        'cost',
        'pdf_path',
        'original_filename'
    ];

    public function maintenanceRecords(){
        return $this->hasMany(VehicleMaintenance::class);
    }
}
