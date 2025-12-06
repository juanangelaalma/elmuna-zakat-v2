<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fidyah extends Model
{
    protected $fillable = [
        'transaction_detail_id',
        'fidyah_type',
        'amount',
        'quantity',
        'unit_type',
    ];
}
