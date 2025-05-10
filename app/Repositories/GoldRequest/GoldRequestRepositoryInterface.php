<?php

namespace App\Repositories\GoldRequest;

use Illuminate\Database\Eloquent\Collection;

interface GoldRequestRepositoryInterface
{
    /**
     * @param array $attributes
     * @return Collection
     */
    public function findMatchingGoldRequests(array $attributes):Collection;

}
