<?php

namespace App\Observers;

use App\Models\Wallet;
use Illuminate\Support\Carbon;

class WalletObserver
{
    /**
     * @param Wallet $wallet
     * @return bool
     */
    public function created(Wallet $wallet): bool
    {
        $inputs['created_at'] = Carbon::now();
        $inputs['wallet_id'] = $wallet->id;
        return $wallet->walletExtensions()
            ->create($inputs);
    }
}
