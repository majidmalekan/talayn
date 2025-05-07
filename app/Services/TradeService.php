<?php

namespace App\Services;

use App\Repositories\Trade\TradeRepositoryInterface;

class TradeService extends BaseService
{
    public function __construct(TradeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
