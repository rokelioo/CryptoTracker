<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CryptoRepositoryInterface;
use App\Repositories\CryptoRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CryptoRepositoryInterface::class, CryptoRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
