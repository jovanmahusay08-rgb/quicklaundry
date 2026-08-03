<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySchedule extends Model
{
    protected $table = 'delivery_schedule';

    protected $fillable = [
        'booking_id', 'scheduled_date', 'scheduled_time', 'delivery_location',
        'delivery_barangay', 'assigned_driver_id', 'status', 'proof_image', 'notes',
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
