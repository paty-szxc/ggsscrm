<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotations extends Model
{
    use HasFactory;

    protected $table = 'quotations';

    protected $fillable = [
        'client',
        'location',
        'date',
        'attachment',
        'revised_attachment',
    ];
}
