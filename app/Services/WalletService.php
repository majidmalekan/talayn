<?php

namespace App\Services;

use App\Repositories\Wallet\WalletRepositoryInterface;

class WalletService extends BaseService
{
    public function __construct(WalletRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
