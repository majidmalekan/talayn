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

    /**
     * @param int $walletId
     * @param int|float $balance
     * @return int
     */
    public function incrementBalance(int $walletId, int|float $balance): int;

    /**
     * @param int $walletId
     * @param int|float $balance
     * @return int
     */
    public function decrementBalance(int $walletId, int|float $balance): int;

    /**
     * @param int $walletId
     * @param int|float $balance
     * @param string $type
     * @return int
     */
    public function incrementBalanceByRelation(int $walletId, int|float $balance , string $type): int;

    /**
     * @param int $walletId
     * @param int|float $balance
     * @param string $type
     * @return int
     */
    public function decrementBalanceByRelation(int $walletId, int|float $balance , string $type): int;


    /**
     * @param int $walletId
     * @param string $extensionType
     * @return Model|null
     */
    public function findWalletExtensionByWalletId(int $walletId, string $extensionType) :?Model;
}
