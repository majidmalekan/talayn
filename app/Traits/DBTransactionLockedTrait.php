<?php

namespace App\Traits;

use Exception as ExceptionAlias;
use Illuminate\Support\Facades\DB;

trait DBTransactionLockedTrait
{
    /**
     * @param ExceptionAlias $exception
     * @throws ExceptionAlias
     */
    public function rollbackError(ExceptionAlias $exception)
    {
        DB::rollBack();
        throw new \Exception($exception->getMessage(), $exception->getCode());
    }
}
