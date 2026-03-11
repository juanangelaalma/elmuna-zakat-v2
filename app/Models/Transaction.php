<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use SoftDeletes;

    protected $guarded = ["id"];

    protected $casts = [
        'is_wa_sent' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function shiftHandover(): BelongsTo
    {
        return $this->belongsTo(ShiftHandover::class);
    }
}
