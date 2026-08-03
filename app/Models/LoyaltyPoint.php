<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $table = 'loyalty_points';

    protected $fillable = [
        'customer_id', 'total_points', 'available_points', 'redeemed_points',
        'last_earned_at', 'last_redeemed_at',
    ];

    protected $casts = [
        'last_earned_at' => 'datetime',
        'last_redeemed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
