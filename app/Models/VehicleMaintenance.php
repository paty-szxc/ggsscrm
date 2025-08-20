<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'file_path',
    ];

    public function vehicle(){
    return $this->belongsTo(CompanyVehicle::class, 'company_vehicle_id');
    }

    // // Accessor for maintenance_url
    // public function getMaintenanceUrlAttribute()
    // {
    //     if ($this->file_path) {
    //         return \Illuminate\Support\Facades\Storage::url($this->file_path);
    //     }
    //     return null;
    // }
}
