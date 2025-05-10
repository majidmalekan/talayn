<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\GoldRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoldRequest>
 */
class GoldRequestFactory extends Factory
{
    protected $model = GoldRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(3, 0.1, 10);
        return [
            'user_id' => User::query()->inRandomOrder()->first()->id ?? User::factory(),
            'status' => StatusEnum::ACTIVE->value,
            'type' => $this->faker->randomElement(['buy', 'sell']),
            'amount' => $amount,
            "remaining_amount" =>$amount,
            'price_fee' => $this->faker->numberBetween(90000000, 120000000),
        ];
    }
}
