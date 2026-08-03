<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $table = 'order_tracking';

    public $timestamps = false;

    protected $fillable = ['booking_id', 'status', 'timestamp', 'notes'];

    protected $casts = ['timestamp' => 'datetime'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
