<?php

use App\Http\Controllers\{PurchaseController, RiceItemController};
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases');
    Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchaseCreate');
    Route::post('purchases/store', [PurchaseController::class, 'store'])->name('purchaseStore');
    
    Route::get('rice-items', [RiceItemController::class, 'index'])->name('riceItems');
    Route::get('rice-items/create', [RiceItemController::class, 'create'])->name('riceItemCreate');
    Route::post('rice-item/store', [RiceItemController::class, 'store'])->name('riceItemStore');
    
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactionCreate');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactionDetail');
    Route::get('transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactionReceipt');
    Route::post('transactions/store', [TransactionController::class, 'store'])->name('transactionStore');

});

require __DIR__.'/settings.php';
