<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;
use App\Traits\CrudForPersonalAccessTokenTrait;
use App\Traits\WalletTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use WalletTrait, CrudForPersonalAccessTokenTrait;

    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials=$this->filterCredentials($request);
            if (!Auth::attempt($credentials)) {
                return failed('', 401);
            }
            $newToken = $this->createANewToken($request->post('username'), Auth::user());
            $wallet = $this->createWalletForNewUser(Auth::user());
            return success('', [
                "access_token" => $newToken,
                "token_type" => env('JWT_TYPE'),
                "expire_in" => env('JWT_TTL'),
                'wallet' => $wallet,
            ]);
        } catch (\Exception $exception) {
            return failed($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    protected function filterCredentials(Request $request): array
    {
        if (filter_var($request->post('username'), FILTER_VALIDATE_EMAIL)) {
            $inputs=['email' => $request->post('username')];
        }elseif(filter_var((int)$request->post('username'), FILTER_VALIDATE_INT)){
            $inputs=['phone' => $request->post('username')];
        }
        else{
            throw new \Exception('Invalid username');
        }
        $inputs['password'] = $request->post('password');
        return $inputs;
    }
}
