<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Traits\WalletTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
{
    use WalletTrait;
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws BindingResolutionException
     */
    public function definition(): array
    {
        return [
            "wallet_number" => $this->generateUniqueNumber(),
            'balance' => $this->faker->randomFloat(2, 10000, 100000000),      // between 10,000 to 100,000,000 Rials
            'gold_balance' => $this->faker->randomFloat(3, 1, 100),
        ];
    }

    /**
     * @throws BindingResolutionException
     */
    private function generateUniqueNumber($length = 16): string
    {
        $number = generate_otp(16);
        while ($this->checkWalletNumberForUniqueness($number)) {
            $number = (generate_otp(16));
        }
        return $number;
    }
}
