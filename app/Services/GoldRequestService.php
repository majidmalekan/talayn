<?php

namespace App\Services;

use App\Repositories\GoldRequest\GoldRequestRepositoryInterface;

class GoldRequestService extends BaseService
{
    public function __construct(GoldRequestRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function findMatchingBuyGoldRequest(array $attributes)
    {
        return $this->repository->findMatchingBuyGoldRequest($attributes);
    }
}
