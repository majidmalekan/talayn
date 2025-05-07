<?php

namespace App\Repositories\GoldRequest;

use App\Models\GoldRequest;
use App\Repositories\BaseRepository;

class GoldRequestRepository extends BaseRepository implements GoldRequestRepositoryInterface
{
    public function __construct(GoldRequest $model)
    {
        parent::__construct($model);
    }
}
