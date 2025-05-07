<?php

namespace App\Providers;

use App\Repositories\BaseEloquentRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\Commission\CommissionRepository;
use App\Repositories\Commission\CommissionRepositoryInterface;
use App\Repositories\GoldRequest\GoldRequestRepository;
use App\Repositories\GoldRequest\GoldRequestRepositoryInterface;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Wallet\WalletRepository;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Repositories\WalletExtension\WalletExtensionRepository;
use App\Repositories\WalletExtension\WalletExtensionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseEloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(WalletExtensionRepositoryInterface::class, WalletExtensionRepository::class);
        $this->app->bind(GoldRequestRepositoryInterface::class, GoldRequestRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(CommissionRepositoryInterface::class, CommissionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
