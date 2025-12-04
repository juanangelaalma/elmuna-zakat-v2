<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRiceAllocation extends Model
{
    protected $guarded = ["id"];

    public function purchaseRice()
    {
        return $this->belongsTo(PurchaseRice::class);
    }

    public function riceSale()
    {
        return $this->belongsTo(RiceSale::class);
    }
}
