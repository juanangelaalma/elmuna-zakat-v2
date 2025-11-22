<?php

namespace App\Providers;

use App\Contracts\PurchaseRiceRepositoryInterface;
use App\Contracts\PurchaseRiceServiceInterface;
use App\Contracts\RiceItemRepositoryInterface;
use App\Contracts\RiceItemServiceInterface;
use App\Repositories\PurchaseRiceRepository;
use App\Repositories\RiceItemRepository;
use App\Services\PurchaseRiceService;
use App\Services\RiceItemService;
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

        $this->app->bind(RiceItemRepositoryInterface::class, RiceItemRepository::class);
        $this->app->bind(RiceItemServiceInterface::class, RiceItemService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
