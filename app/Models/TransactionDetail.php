<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use SoftDeletes;

    protected $guarded = ["id"];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function rice()
    {
        return $this->hasOne(Rice::class);
    }

    public function riceSale()
    {
        return $this->hasOne(RiceSale::class);
    }

    public function donation()
    {
        return $this->hasOne(Donation::class);
    }

    public function fidyah()
    {
        return $this->hasOne(Fidyah::class);
    }

    public function wealth()
    {
        return $this->hasOne(Wealth::class);
    }
}
