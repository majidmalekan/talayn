<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WalletRepository extends BaseRepository implements WalletRepositoryInterface
{
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }

    public function checkWalletNumber(string $walletNumber): bool
    {
        return $this->model
            ->query()
            ->where('wallet_number', $walletNumber)
            ->exists();
    }

    /**
     * @param array $attributes
     * @return Model|null
     */
    public function firstOrCreate(array $attributes ): ?Model
    {
        return $this->model
            ->query()
            ->firstOrCreate(['user_id' => $attributes["user_id"]], $attributes);
    }


    /**
     * @param int $userId
     * @return Model|null
     */
    public function findWalletByUserId(int $userId): ?Model
    {
        return $this->model
            ->query()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * @param int $walletNumber
     * @return Model|null
     */
    public function findWalletByWalletNumber(int $walletNumber): ?Model
    {
        return $this->model
            ->query()
            ->where('wallet_number', $walletNumber)
            ->first();
    }

    /**
     * @param int $walletId
     * @param int|float $balance
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function incrementBalance(int $walletId, int|float $balance): int
    {
        $this->clearCache('find', $walletId);
        $this->clearCache('index');
        return $this->model
            ->query()
            ->where('id', $walletId)
            ->increment('balance', $balance);
    }

    /**
     * @param int $walletId
     * @param int|float $balance
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function decrementBalance(int $walletId, int|float $balance): int
    {
        $this->clearCache('find', $walletId);
        $this->clearCache('index');
        return $this->model
            ->query()
            ->where('id', $walletId)
            ->decrement('balance', $balance);
    }

    /**
     * @param int $walletId
     * @param int|float $balance
     * @param string $type
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function decrementBalanceByRelation(int $walletId, int|float $balance , string $type): int
    {

        $this->clearCache('find', $walletId);
        $this->clearCache('index');
        $wallet=$this->show($walletId);
        return $wallet->walletExtensions()
            ->newQuery()
            ->where('wallet_id', $walletId)
            ->where('type', $type)
            ->decrement('balance', $balance);
    }

    /**
     * @param int $walletId
     * @param int|float $balance
     * @param string $type
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function incrementBalanceByRelation(int $walletId, int|float $balance , string $type): int
    {

        $this->clearCache('find', $walletId);
        $this->clearCache('index');
        $wallet=$this->show($walletId);
        return $wallet->walletExtensions()
            ->newQuery()
            ->where('wallet_id', $walletId)
            ->where('type', $type)
            ->increment('balance', $balance);
    }


    /**
     * @param int $walletId
     * @param string $extensionType
     * @return Model|null
     */
    public function findWalletExtensionByWalletId(int $walletId, string $extensionType) :?Model
    {
        $wallet=$this->show($walletId);
        return $wallet->walletExtensions()
            ->newQuery()
            ->where('wallet_id', $walletId)
            ->where('type', $extensionType)
            ->firstOrFail();
    }

    public function lockForUpdate(int $userId)
    {
       return $this->model
           ->query()
           ->where('user_id',$userId)
           ->lockForUpdate()
           ->firstOrFail();
    }
}
