<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    public function firstOrCreate(array $attributes): ?Model
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

    public function lockForUpdate(int $userId, $balance, $goldBalance): int
    {
        $fieldsToUpdate = $userId == auth('sanctum')->id()
            ? [
                'gold_balance' => DB::raw("gold_balance + $goldBalance"),
                'balance' => DB::raw("balance - $balance")
            ]
            : [
                'gold_balance' => DB::raw("gold_balance - $goldBalance"),
                'balance' => DB::raw("balance + $balance")
            ];
        return $this->model
            ->query()
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->update($fieldsToUpdate);
    }
}
