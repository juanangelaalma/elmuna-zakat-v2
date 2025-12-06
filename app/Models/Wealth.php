<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wealth extends Model
{
    protected $fillable = [
        'transaction_detail_id',
        'amount',
    ];
}
