<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultValue extends Model
{
    protected $fillable = [
        'rice_sales_quantity',
        'rice_sales_amount',
        'rice_quantity',
        'fidyah_quantity',
        'fidyah_amount',
        'unit',
    ];
}
