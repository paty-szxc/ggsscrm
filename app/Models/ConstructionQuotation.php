<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionQuotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'construction_quotations';

    protected $fillable = [
        'client',
        'location',
        'date',
        'attachment',
        'revised_attachment',
    ];
}
