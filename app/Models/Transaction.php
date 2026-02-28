<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $guarded = ["id"];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
