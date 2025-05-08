<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\Setting;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Wallet;
use App\Models\WalletExtension;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            $user->wallet()->save(Wallet::factory()->create());
        });

        Commission::query()->insert([
            ['from_gram' => 0, 'to_gram' => 1, 'percent' => 2],
            ['from_gram' => 1, 'to_gram' => 10, 'percent' => 1.5],
            ['from_gram' => 10, 'to_gram' => null, 'percent' => 1],
        ]);

        Setting::query()->insert([
            ['key' => 'min_commission', 'value' => '50000'],
            ['key' => 'max_commission', 'value' => '5000000'],
        ]);
    }
}
