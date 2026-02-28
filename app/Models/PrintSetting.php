<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintSetting extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'protocol',
        'paper_size',
        'is_default',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'port'       => 'integer',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    /**
     * Scope: only active printers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: only the default printer.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    // ── Methods ────────────────────────────────────────────────────────────────

    /**
     * Check whether the printer is reachable on the network.
     * Uses a TCP connection attempt with a 3-second timeout.
     */
    public function isOnline(): bool
    {
        try {
            $connection = @fsockopen($this->ip_address, $this->port, $errno, $errstr, 3);
            if ($connection) {
                fclose($connection);
                return true;
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
