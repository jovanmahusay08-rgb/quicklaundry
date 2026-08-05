<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'booking_reference', 'service_id', 'quantity_kg', 'service_type',
        'pickup_date', 'pickup_time', 'delivery_date', 'delivery_time', 'service_price',
        'is_rush_service', 'rush_fee', 'priority_level', 'subtotal', 'discount', 'total_amount',
        'status', 'payment_status', 'payment_method', 'delivery_address', 'delivery_municipality',
        'delivery_barangay', 'delivery_purok', 'location_latitude', 'location_longitude',
        'live_latitude', 'live_longitude', 'location_accuracy', 'location_updated_at',
        'delivery_phone', 'recipient_name', 'special_instructions', 'estimated_completion',
        'assigned_staff_id', 'assigned_driver_id', 'notes', 'completed_at',
    ];

    protected $casts = [
        'is_rush_service' => 'boolean',
        'pickup_date' => 'date',
        'delivery_date' => 'date',
        'estimated_completion' => 'datetime',
        'completed_at' => 'datetime',
        'location_latitude' => 'decimal:7',
        'location_longitude' => 'decimal:7',
        'live_latitude' => 'decimal:7',
        'live_longitude' => 'decimal:7',
        'location_accuracy' => 'decimal:2',
        'location_updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function (self $booking) {
            if ($booking->isDirty('payment_status') && $booking->payment_status === 'Paid' && $booking->getOriginal('payment_status') !== 'Paid') {
                Customer::where('id', $booking->customer_id)
                    ->increment('total_spent', $booking->total_amount);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public static function generateBookingReference(): string
    {
        $date = now()->format('Ymd');
        $prefix = "BK-{$date}-";

        $latestReference = static::where('booking_reference', 'like', "$prefix%")
            ->orderBy('booking_reference', 'desc')
            ->value('booking_reference');

        $sequence = 1;
        if ($latestReference) {
            $sequence = (int) substr($latestReference, strlen($prefix)) + 1;
        }

        return sprintf('%s%04d', $prefix, $sequence);
    }

    public function service()
    {
        return $this->belongsTo(LaundryService::class, 'service_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function assignedDriver()
    {
        return $this->belongsTo(Staff::class, 'assigned_driver_id');
    }

    public function pickupSchedule()
    {
        return $this->hasOne(PickupSchedule::class);
    }

    public function deliverySchedule()
    {
        return $this->hasOne(DeliverySchedule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function tracking()
    {
        return $this->hasMany(OrderTracking::class)->orderBy('timestamp');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
