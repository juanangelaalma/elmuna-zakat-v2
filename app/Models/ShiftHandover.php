<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftHandover extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'handover_date'          => 'date',
        'total_rice_sale_amount' => 'decimal:2',
        'total_wealth_amount'    => 'decimal:2',
        'total_fidyah_amount'    => 'decimal:2',
        'total_donation_amount'  => 'decimal:2',
        'total_rice_quantity'    => 'decimal:2',
        'total_fidyah_quantity'  => 'decimal:2',
        'total_donation_quantity'=> 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
