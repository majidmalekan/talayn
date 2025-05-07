<?php

namespace App\Repositories\WalletExtension;

use App\Models\WalletExtension;
use App\Repositories\BaseRepository;

class WalletExtensionRepository extends BaseRepository implements WalletExtensionRepositoryInterface
{
    public function __construct(WalletExtension $model)
    {
        parent::__construct($model);
    }
}
