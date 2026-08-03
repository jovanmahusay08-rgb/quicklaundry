<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staff';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'profile_image',
        'role', 'address', 'barangay', 'is_active', 'salary', 'hire_date', 'last_login',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'hire_date' => 'date',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function assignedBookings()
    {
        return $this->hasMany(Booking::class, 'assigned_staff_id');
    }

    public function driverBookings()
    {
        return $this->hasMany(Booking::class, 'assigned_driver_id');
    }

    public function pickups()
    {
        return $this->hasMany(PickupSchedule::class, 'assigned_driver_id');
    }

    public function deliveries()
    {
        return $this->hasMany(DeliverySchedule::class, 'assigned_driver_id');
    }
}
