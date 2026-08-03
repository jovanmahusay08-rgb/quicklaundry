<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryService extends Model
{
    use HasFactory;

    public const PRICING_VARIABLE = 'variable';
    public const PRICING_FLAT = 'flat';
    public const BASE_WEIGHT_KG = 7.0;
    public const MAX_WEIGHT_KG = 9.0;
    public const STANDARD_LOAD_PRICE = 200.0;
    public const LARGE_LOAD_PRICE = 400.0;

    protected $table = 'laundry_services';

    protected $fillable = [
        'service_name', 'description', 'base_price', 'price_per_kilo',
        'pricing_type', 'estimated_days', 'is_active', 'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'price_per_kilo' => 'decimal:2',
        'pricing_type' => 'string',
    ];

    public function calculatePrice(float $quantity): array
    {
        if ($quantity > self::MAX_WEIGHT_KG) {
            throw new \InvalidArgumentException('QuickWash only accepts laundry loads up to 9 kg.');
        }

        $fixedPrice = $quantity <= self::BASE_WEIGHT_KG
            ? self::STANDARD_LOAD_PRICE
            : self::LARGE_LOAD_PRICE;

        return [
            'service_price' => $fixedPrice,
            'extra_charge' => 0.0,
            'subtotal' => $fixedPrice,
            'total_amount' => $fixedPrice,
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'service_id');
    }
}
