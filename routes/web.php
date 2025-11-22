<?php

use App\Http\Controllers\{PurchaseController, RiceItemController};
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
});

require __DIR__.'/settings.php';
