<?php

namespace App\Providers;

use App\Contracts\{
    PurchaseRiceRepositoryInterface,
    PurchaseRiceServiceInterface,
    RiceItemRepositoryInterface,
    RiceItemServiceInterface,
    RiceRepositoryInterface,
    RiceSaleRepositoryInterface,
    RiceSaleServiceInterface,
    RiceServiceInterface,
    TransactionDetailRepositoryInterface,
    TransactionDetailServiceInterface,
    TransactionRepositoryInterface,
    TransactionServiceInterface
};
use App\Repositories\{
    PurchaseRiceRepository,
    RiceItemRepository,
    RiceRepository,
    RiceSaleRepository,
    TransactionDetailRepository,
    TransactionRepository
};
use App\Services\{
    PurchaseRiceService,
    RiceItemService,
    RiceService,
    RiceSaleService,
    TransactionDetailService,
    TransactionService
};

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

        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(TransactionDetailRepositoryInterface::class, TransactionDetailRepository::class);

        $this->app->bind(RiceSaleRepositoryInterface::class, RiceSaleRepository::class);
        $this->app->bind(RiceSaleServiceInterface::class, RiceSaleService::class);

        $this->app->bind(TransactionDetailServiceInterface::class, TransactionDetailService::class);
        $this->app->bind(RiceRepositoryInterface::class, RiceRepository::class);
        $this->app->bind(RiceServiceInterface::class, RiceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
