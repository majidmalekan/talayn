<?php

namespace Database\Factories;

use App\Models\GoldRequest;
use App\Models\Trade;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trade>
 */
class TradeFactory extends Factory
{
    protected $model = Trade::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws BindingResolutionException
     */
    public function definition(): array
    {
        $buyer = GoldRequest::factory()->create(['type' => 'buy']);
        $seller = GoldRequest::factory()->create(['type' => 'sell', 'price_fee' => $buyer->price_fee]);

        $amount = $this->faker->randomFloat(3, 0.1, min($buyer->amount, $seller->amount));
        $priceFee = $buyer->price_fee;
        $totalPrice = round($amount * $priceFee, 2);
        $commission = calculateDynamicCommission($amount, $totalPrice);

        return [
            'buy_gold_request_id' => $buyer->id,
            'sell_gold_request_id' => $seller->id,
            'amount' => $amount,
            'price_fee' => $priceFee,
            'total_price' => $totalPrice,
            'commission' => $commission,
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
