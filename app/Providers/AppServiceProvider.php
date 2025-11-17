<?php

namespace App\Providers;

use App\Contracts\PurchaseRiceRepositoryInterface;
use App\Contracts\PurchaseRiceServiceInterface;
use App\Repositories\PurchaseRiceRepository;
use App\Services\PurchaseRiceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PurchaseRiceRepositoryInterface::class, PurchaseRiceRepository::class);
        $this->app->bind(PurchaseRiceServiceInterface::class, PurchaseRiceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
