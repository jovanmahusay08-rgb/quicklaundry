<?php

namespace Tests\Unit;

use App\Models\LaundryService;
use PHPUnit\Framework\TestCase;

class LaundryServicePricingTest extends TestCase
{
    public function test_base_price_covers_up_to_seven_kilograms(): void
    {
        $service = new LaundryService([
            'base_price' => 200,
            'price_per_kilo' => 30,
            'pricing_type' => LaundryService::PRICING_VARIABLE,
        ]);

        $this->assertSame(200.0, $service->calculatePrice(7)['total_amount']);
    }

    public function test_weight_above_seven_and_up_to_nine_kilograms_costs_four_hundred(): void
    {
        $service = new LaundryService([
            'base_price' => 200,
            'price_per_kilo' => 30,
            'pricing_type' => LaundryService::PRICING_VARIABLE,
        ]);

        $this->assertSame(400.0, $service->calculatePrice(8)['total_amount']);
        $this->assertSame(400.0, $service->calculatePrice(9)['total_amount']);
    }

    public function test_weight_above_nine_kilograms_is_rejected(): void
    {
        $service = new LaundryService();

        $this->expectException(\InvalidArgumentException::class);
        $service->calculatePrice(9.1);
    }
}
