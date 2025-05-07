<?php

namespace App\Services;

use App\Repositories\Setting\SettingRepositoryInterface;

class SettingService extends BaseService
{
    public function __construct(SettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
