<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'payment_reference', 'amount', 'payment_method', 'status', 'notes',
        'gcash_sender_number', 'gcash_reference', 'proof_image', 'submitted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getCashReceivedAttribute(): ?float
    {
        if ($this->payment_method !== 'Cash on Delivery') {
            return null;
        }

        if (preg_match('/Amount Received:\\s*(?:\\x{20B1})?([\\d,]+(?:\\.\\d{1,2})?)/u', (string) $this->notes, $matches)) {
            return (float) str_replace(',', '', $matches[1]);
        }

        return null;
    }

    public function getChangeAmountAttribute(): ?float
    {
        return $this->cash_received === null
            ? null
            : max($this->cash_received - (float) $this->amount, 0);
    }
}
