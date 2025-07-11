<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeSupplies extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'office_supplies';

    protected $fillable = [
        'item_name',
        'item_cost',
        'quantity',
        'unit',
        'care_of',
        'remarks',
    ];
}
