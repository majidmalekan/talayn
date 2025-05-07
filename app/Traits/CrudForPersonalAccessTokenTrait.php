<?php

namespace App\Traits;

use App\Services\UserService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait CrudForPersonalAccessTokenTrait
{
    /**
     * @param $token
     * @return Model|null
     */
    protected function getPersonalAccessToken($token): ?Model
    {
        return DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();
    }

    protected function deleteExpireToken($tokenableId): int
    {
        return DB::table('personal_access_tokens')
            ->where('tokenable_id', $tokenableId)
            ->delete();
    }

    /**
     * @throws BindingResolutionException
     */
    protected function createANewToken($tokenable_id, Model|null $user)
    {
        try {
            DB::beginTransaction();
            $userFind = $user != null ? $user : app()
                ->make(UserService::class)
                ->find(auth('sanctum')->user()?->id);
            $this->deleteExpireToken($userFind->id);
            $token= $userFind->createToken($tokenable_id)
                ->plainTextToken;
            DB::commit();
            return $token;
        }
        catch (\Exception $e) {
            DB::rollBack();
          return failed($e->getMessage(),401);
        }

    }
}
