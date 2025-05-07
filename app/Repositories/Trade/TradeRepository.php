<?php

namespace App\Repositories\Trade;

use App\Models\Trade;
use App\Repositories\BaseRepository;

class TradeRepository extends BaseRepository implements TradeRepositoryInterface
{
    public function __construct(Trade $model)
    {
        parent::__construct($model);
    }
}
