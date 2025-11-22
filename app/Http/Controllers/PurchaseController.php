<?php

namespace App\Http\Controllers;

use App\Contracts\PurchaseRiceServiceInterface;
use App\Contracts\RiceItemServiceInterface;
use App\Http\Requests\PurchaseStoreRequest;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseRiceServiceInterface $service,
        private RiceItemServiceInterface $riceItemService,
    ) {}

    public function index()
    {
        $purchases = $this->service->getAllPurchaseRiceWithRiceItem();
        $totalQuantity = $purchases->sum('quantity');
        $totalValue = $purchases->sum(fn($item) => $item->quantity * $item->price_per_kg);
        $numberOfTransactions = $purchases->count();

        return Inertia::render('purchases/purchases', compact('purchases', 'totalQuantity', 'totalValue', 'numberOfTransactions'));
    }

    public function create()
    {
        $riceItems = $this->riceItemService->getAllRiceItems();
        return Inertia::render('purchases/create-purchase', compact('riceItems'));
    }

    public function store(PurchaseStoreRequest $request)
    {
        $validatedData = $request->validated();
        $userId = auth()->user()->id;
        $validatedData['created_by'] = $userId;

        $this->service->createPurchaseRice($validatedData);

        return redirect()->route('purchases');
    }

}
