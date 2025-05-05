<?php

namespace App\Providers;

use App\Repository\BaseEloquentRepositoryInterface;
use App\Repository\BaseRepository;
use App\Repository\Faq\FaqRepository;
use App\Repository\Faq\FaqRepositoryInterface;
use App\Repository\NetworkLog\NetworkLogRepository;
use App\Repository\NetworkLog\NetworkLogRepositoryInterface;
use App\Repository\Ticket\TicketRepository;
use App\Repository\Ticket\TicketRepositoryInterface;
use App\Repository\User\UserRepository;
use App\Repository\User\UserRepositoryInterface;
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
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(NetworkLogRepositoryInterface::class, NetworkLogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
