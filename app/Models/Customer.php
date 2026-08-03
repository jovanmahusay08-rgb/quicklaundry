<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'customers';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'profile_image',
        'address', 'barangay', 'is_active', 'loyalty_points', 'total_spent', 'last_login',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'total_spent' => 'decimal:2',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getTotalSpentAttribute($value)
    {
        if ((float) $value > 0) {
            return $value;
        }

        return $this->bookings()->where('payment_status', 'Paid')->sum('total_amount');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function loyalty()
    {
        return $this->hasOne(LoyaltyPoint::class);
    }
}
