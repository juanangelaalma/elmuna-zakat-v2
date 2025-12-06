<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rice extends Model
{
    protected $table = 'rices';

    protected $fillable = [
        'transaction_detail_id',
        'quantity',
        'unit_type',
    ];
}
