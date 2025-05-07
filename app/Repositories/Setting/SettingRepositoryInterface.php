<?php

namespace App\Repositories\Setting;

interface SettingRepositoryInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function firstByKey(string $key): mixed;
}
