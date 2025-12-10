<?php

use App\Http\Controllers\{PurchaseController, RiceItemController, DashboardController};
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\DefaultValueController;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases');
    Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchaseCreate');
    Route::post('purchases/store', [PurchaseController::class, 'store'])->name('purchaseStore');
    
    Route::get('rice-items', [RiceItemController::class, 'index'])->name('riceItems');
    Route::get('rice-items/create', [RiceItemController::class, 'create'])->name('riceItemCreate');
    Route::post('rice-item/store', [RiceItemController::class, 'store'])->name('riceItemStore');
    
    Route::get('transactions/index', [TransactionController::class, 'index'])->name('transactions');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactionCreate');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactionDetail');
    Route::get('transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactionReceipt');
    Route::post('transactions/store', [TransactionController::class, 'store'])->name('transactionStore');
    
    // Transaction detail routes
    Route::get('transactions/details/rice-sales', [TransactionController::class, 'riceSales'])->name('transactions.riceSales');
    Route::get('transactions/details/rices', [TransactionController::class, 'rice'])->name('transactions.rice');
    Route::get('transactions/details/donations', [TransactionController::class, 'donations'])->name('transactions.donations');
    Route::get('transactions/details/fidyahs', [TransactionController::class, 'fidyahs'])->name('transactions.fidyahs');
    Route::get('transactions/details/wealths', [TransactionController::class, 'wealths'])->name('transactions.wealths');
    
    // Export routes
    Route::get('transactions/details/rice-sales/export', [TransactionController::class, 'exportRiceSales'])->name('transactions.riceSales.export');
    Route::get('transactions/details/rices/export', [TransactionController::class, 'exportRice'])->name('transactions.rice.export');
    Route::get('transactions/details/donations/export', [TransactionController::class, 'exportDonations'])->name('transactions.donations.export');
    Route::get('transactions/details/fidyahs/export', [TransactionController::class, 'exportFidyahs'])->name('transactions.fidyahs.export');
    Route::get('transactions/details/wealths/export', [TransactionController::class, 'exportWealths'])->name('transactions.wealths.export');

    Route::get('default-value', [DefaultValueController::class, 'index'])->name('defaultValue');
    Route::patch('default-value', [DefaultValueController::class, 'update'])->name('defaultValueUpdate');
});

require __DIR__.'/settings.php';
