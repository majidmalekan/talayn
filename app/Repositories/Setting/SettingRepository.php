<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use App\Repositories\BaseRepository;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function firstByKey(string $key): mixed
    {
       return $this->model->query()
           ->where('key', $key)
           ->value('value');
    }
}
