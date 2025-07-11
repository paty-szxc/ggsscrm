<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HouseAndLot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'house_and_lot';

    protected $fillable = [
        'address',
        'cost'
    ];
}
