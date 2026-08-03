<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupSchedule extends Model
{
    protected $table = 'pickup_schedule';

    protected $fillable = [
        'booking_id', 'scheduled_date', 'scheduled_time', 'pickup_location',
        'pickup_barangay', 'assigned_driver_id', 'status', 'proof_image', 'notes',
    ];

    protected $casts = ['scheduled_date' => 'date'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver()
    {
        return $this->belongsTo(Staff::class, 'assigned_driver_id');
    }
}
