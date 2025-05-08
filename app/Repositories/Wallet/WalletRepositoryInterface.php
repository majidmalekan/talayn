<?php

namespace App\Repositories\Wallet;

use Illuminate\Database\Eloquent\Model;

interface WalletRepositoryInterface
{
    /**
     * @param string $walletNumber
     * @return bool
     */
    public function checkWalletNumber(string $walletNumber): bool;

    /**
     * @param array $attributes
     * @return Model|null
     */
    public function firstOrCreate(array $attributes): ?Model;

    /**
     * @param int $userId
     * @return Model|null
     */
    public function findWalletByUserId(int $userId): ?Model;

    /**
     * @param int $walletNumber
     * @return Model|null
     */
    public function findWalletByWalletNumber(int $walletNumber): ?Model;
}
