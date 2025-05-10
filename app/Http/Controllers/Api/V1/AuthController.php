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
use Symfony\Component\Yaml\Yaml;

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
                return failed('auth.failed', 401);
            }
            $newToken = $this->createANewToken($request->post('username'), Auth::user());
            $wallet = $this->createWalletForNewUser(Auth::user());
            return success('', [
                "access_token" => $newToken,
                "token_type" => config('sanctum.type'),
                "expire_in" => config('sanctum.expiration'),
                'wallet' => $wallet,
            ]);
        } catch (\Exception $exception) {
            return failed(__('serverError.server_error'));
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
            throw new \Exception(__('auth.failed'));
        }
        $inputs['password'] = $request->post('password');
        return $inputs;
    }

    public function yamlConvertor(): JsonResponse
    {
        $yamlFilePath = resource_path('swagger/openapi.yaml');
        $yaml = Yaml::parse(file_get_contents($yamlFilePath));
        $json = json_encode($yaml, JSON_PRETTY_PRINT);
        file_put_contents(storage_path('api-docs/api-docs.json'), $json);
        return success('your api docs has been converted');
    }
}
