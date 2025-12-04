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
use App\Services\TransactionService;
use App\Contracts\TransactionServiceInterface;
use App\Contracts\TransactionRepositoryInterface;
use App\Repositories\TransactionRepository;
use App\Contracts\TransactionDetailRepositoryInterface;
use App\Repositories\TransactionDetailRepository;
use App\Contracts\RiceSaleRepositoryInterface;
use App\Contracts\RiceSaleServiceInterface;
use App\Repositories\RiceSaleRepository;
use App\Services\RiceSaleService;
use App\Services\TransactionDetailService;
use App\Contracts\TransactionDetailServiceInterface;

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

        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(TransactionDetailRepositoryInterface::class, TransactionDetailRepository::class);

        $this->app->bind(RiceSaleRepositoryInterface::class, RiceSaleRepository::class);
        $this->app->bind(RiceSaleServiceInterface::class, RiceSaleService::class);

        $this->app->bind(TransactionDetailServiceInterface::class, TransactionDetailService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
