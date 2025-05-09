<?php

namespace Database\Factories;

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
        return [
            'user_id' => User::query()->inRandomOrder()->first()->id ?? User::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'completed']),
            'type' => $this->faker->randomElement(['buy', 'sell']),
            'amount' => $this->faker->randomFloat(3, 0.1, 10),
            'price_fee' => $this->faker->numberBetween(90000000, 120000000),
        ];
    }
}
