<?php

namespace Database\Factories;

use App\Models\GoldRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoldRequest>
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
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(), // اگر کاربر نداریم، بساز
            'status' => $this->faker->randomElement(['active', 'inactive', 'completed']),
            'type' => $this->faker->randomElement(['buy', 'sell']),
            'amount' => $this->faker->randomFloat(3, 0.1, 10), // مثلاً بین 0.1 تا 10 گرم
            'price_fee' => $this->faker->numberBetween(9000000, 12000000), // فرض نرخ طلا
        ];
    }
}
