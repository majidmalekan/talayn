<?php

namespace App\Traits;

use App\Models\Wallet;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Wallet\WalletRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

trait WalletTrait
{
    /**
     * @param int $userId
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getWalletByUserId(int $userId): ?Model
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->findWalletByUserId($userId);
    }

    /**
     * @param string $walletNumber
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getWalletByWalletNumber(string $walletNumber): ?Model
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->findWalletByWalletNumber($walletNumber);
    }

    /**
     * @param string $phoneNumber
     * @param string $type
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getWalletByPhoneNumber(string $phoneNumber, string $type = 'transaction'): ?Model
    {
        $user = app()
            ->make(UserRepositoryInterface::class)
            ->firstByPhone($phoneNumber);
        if (!$user) {
            return null;
        }
        return $this->getWalletByUserId($user->id);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getWallet(string $mixed): ?Model
    {
        /* @var Wallet $wallet */
        $wallet = $this->getWalletByWalletNumber($mixed);
        if ($wallet) {
            return $wallet;
        }
        $wallet = $this->getWalletByUserId($mixed);
        if ($wallet) {
            return $wallet;
        }
        $wallet = $this->getWalletByPhoneNumber($mixed);
        if ($wallet) {
            return $wallet;
        }
        return null;
    }

    /**
     * @param Model|null $user
     * @return Model
     * @throws BindingResolutionException
     */
    public function createWalletForNewUser(?Model $user): Model
    {

        $inputs["user_id"] = $user->id;
        $inputs["wallet_number"] = generateRandom(16);
        while ($this->checkWalletNumberForUniqueness($inputs["wallet_number"])) {
            $inputs["wallet_number"] = generateRandom(16);
        }
        return app()
            ->make(WalletRepositoryInterface::class)
            ->firstOrCreate($inputs);
    }

    /**
     * @param string $walletNumber
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function checkWalletNumberForUniqueness(string $walletNumber): mixed
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->checkWalletNumber($walletNumber);
    }

    /**
     * @param int $userId
     * @param float|int $balance
     * @param float|int $goldBalance
     * @param bool $isBuyer
     * @return int|null
     * @throws BindingResolutionException
     */
    public function lockForUpdateWallet(int $userId,float|int $balance,float|int $goldBalance,bool $isBuyer): ?int
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->lockForUpdate($userId,$balance,$goldBalance,$isBuyer);
    }
}
