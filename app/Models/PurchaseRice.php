<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRice extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseRiceFactory> */
    use HasFactory;

    protected $table = 'purchase_rices';

    public function riceItem()
    {
        return $this->belongsTo(RiceItem::class);
    }
}
