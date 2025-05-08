<?php

namespace App\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepositoryInterface;

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
            ->findWalletByWalletNumber(Crypt::encryptString($walletNumber));
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
    protected function getWallet(string $mixed, string $type = 'transaction'): ?Model
    {
        /* @var Wallet $wallet */
        $wallet = $this->getWalletByWalletNumber($mixed);
        if ($wallet) {
            return $wallet;
        }
        $wallet = $this->getWalletByUserId($mixed, $type);
        if ($wallet) {
            return $wallet;
        }
        $wallet = $this->getWalletByPhoneNumber($mixed, $type);
        if ($wallet) {
            return $wallet;
        }
        return null;
    }

    /**
     * @throws BindingResolutionException
     */
    public function decrementBalanceOfWallet(int $walletId, int|float $balance, float $goldAmount)
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->decrementBalance($walletId, $balance, $goldAmount);
    }

    /**
     * @throws BindingResolutionException
     */
    public function incrementBalanceOfWallet(int $walletId, int|float $balance, float $goldAmount)
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->incrementBalance($walletId, $balance, $goldAmount);
    }

    /**
     * @param Model|null $user
     * @return Model
     * @throws BindingResolutionException
     */
    public function createWalletForNewUser(?Model $user): Model
    {

        $inputs["user_id"] = $user->id;
        $inputs["wallet_number"] = (generate_otp(16));
        while ($this->checkWalletNumberForUniqueness($inputs["wallet_number"])) {
            $inputs["wallet_number"] = (generate_otp(16));
        }
        $wallet = app()
            ->make(WalletRepositoryInterface::class)
            ->firstOrCreate($inputs);
        return $wallet;
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
     * @return Model
     * @throws BindingResolutionException
     */
    public function lockForUpdateWallet(int $userId,float|int $balance,float|int $goldBalance): Model
    {
        return app()
            ->make(WalletRepositoryInterface::class)
            ->lockForUpdate($userId,$balance,$goldBalance);
    }
}
