<?php

namespace App\Repositories\Commission;

use Illuminate\Database\Eloquent\Model;

interface CommissionRepositoryInterface
{
    /**
     * @param float $amountGram
     * @return Model|null
     */
    public function firstByRule(float $amountGram): ?Model;

}
