<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Otp\VerifyOtpRequest;

trait VerifyByOtp
{

    /**
     * Generate new OTP (deletes previous ones)
     *
     * @param int|string $phone
     * @return string
     * @throws Exception
     */
    public function deleteAndGenerateOtp(int|string $phone): string
    {
        try {
//            $otp = 1111;
            $otp = generate_otp(env("OTP_LENGTH"));
            if (Cache::has($phone))
                Cache::pull($phone);
            Cache::put($phone, $otp, env('OTP_EXPIRES_IN'));
            return $otp;
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage(), 403);
        }
    }

    /**
     * @param string $phone
     * @return void
     * @throws Exception
     */
    protected function sendOtpVerification(string $phone): void
    {
        $this->sendVerificationNotification($phone);
    }

    /**
     * @throws Exception
     * @throws Exception
     */
    protected function verifyOtp(VerifyOtpRequest $request): array|bool
    {
        $otp = $request->post('otp');
        if ($this->otpVerify($request->post('phone'), $otp)) {
            try {
                $user = $this->service->firstOrCreate(['phone' => $request->post('phone')]);
                $user->markContactAsVerified();
                Auth::guard('web')->attempt(
                    [
                        "phone" => $request->post('phone'),
                        "password" => $request->post('phone')
                    ],
                    true);
                Cookie::queue('remember_token', Auth::guard('web')->user()->getRememberToken()
                    , 60 * 24 * 30, null, null, true, true);
                return ["status" => true, "user" => $user];
            } catch (Exception $exception) {
                DB::rollBack();
                throw new Exception($exception->getMessage(), 403);
            }
        }
        return ["status" => false, "user" => null];
    }
}
