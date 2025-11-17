<?php

namespace App\Http\Controllers;

use App\Contracts\PurchaseRiceServiceInterface;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseRiceServiceInterface $service
    ) {}

    public function index()
    {
        $purchases = $this->service->getAllPurchaseRiceWithRiceItem();
        $totalQuantity = $purchases->sum('quantity');
        $totalValue = $purchases->sum(fn($item) => $item->quantity * $item->price_per_kg);
        $numberOfTransactions = $purchases->count();

        return Inertia::render('purchases/purchases', compact('purchases', 'totalQuantity', 'totalValue', 'numberOfTransactions'));
    }

}
