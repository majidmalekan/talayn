<?php

namespace App\Services;

use App\Repositories\Commission\CommissionRepositoryInterface;

class CommissionService extends BaseService
{
    public function __construct(CommissionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
