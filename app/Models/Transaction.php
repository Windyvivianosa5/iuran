<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'transaction_id',
        'gross_amount',
        'payment_type',
        'payment_method',
        'status',
        'snap_token',
        'description',
        'bulan_pembayaran',
        'transaction_time',
        'settlement_time',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
    ];

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccess(): bool
    {
        return $this->status === 'settlement';
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['cancel', 'deny', 'expire', 'failure']);
    }
}
