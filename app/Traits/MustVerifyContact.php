<?php

namespace App\Traits;

use App\Notifications\SendSmsNotification;
use App\Repository\User\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\Pure;


trait MustVerifyContact
{
    /**
     * @return bool
     */
    #[Pure]
    public function hasVerifiedContact(): bool
    {
        return $this->hasVerifiedPhone();
    }

    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Mark the given user's contact as verified.
     * @param string|null $userCredentials
     * @return bool
     */
    public function markContactAsVerified(string $userCredentials = null): bool
    {
        if ($userCredentials == null) {
            if (!$this->hasVerifiedPhone()) {
                return $this->markPhoneAsVerified();
            }
        }
        return false;
    }

    /**
     * Mark the given user's phone number as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * @param int|string $userCredentials
     * @return void
     * @throws Exception
     */
    public function sendVerificationNotification(int|string $userCredentials): void
    {
         $this->notify(new SendSmsNotification(275055,$userCredentials));
    }

    /**
     * @param int|string $phone
     * @param int $otp
     * @return bool
     */
    public function otpVerify(int|string $phone, int $otp): bool
    {
        return Cache::get($phone) == $otp;
    }

    public function getAdmin()
    {
       return app()->make(UserRepositoryInterface::class)->getAdmin();
    }
}
