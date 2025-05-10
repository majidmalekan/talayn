<?php

namespace App\Repositories\GoldRequest;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface GoldRequestRepositoryInterface
{
    /**
     * @param array $attributes
     * @return Collection
     */
    public function findMatchingBuyGoldRequest(array $attributes):Collection;

}
