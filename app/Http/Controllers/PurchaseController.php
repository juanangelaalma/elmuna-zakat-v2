<?php

namespace App\Http\Controllers;

use App\Contracts\PurchaseRiceServiceInterface;
use App\Contracts\RiceItemServiceInterface;
use App\Http\Requests\PurchaseStoreRequest;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

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
        $totalStocks = $this->service->getTotalStocks();

        $visiblePurchased = \App\Models\PurchaseRice::where('is_visible', true)->sum('quantity');
        $visibleAllocated = \App\Models\PurchaseRiceAllocation::whereHas('purchaseRice', fn($q) => $q->where('is_visible', true))->sum('quantity');
        $visibleAvailableStock = max(0, $visiblePurchased - $visibleAllocated);

        $amilPurchased = \App\Models\PurchaseRice::where('is_visible', false)->sum('quantity');
        $amilAllocated = \App\Models\PurchaseRiceAllocation::whereHas('purchaseRice', fn($q) => $q->where('is_visible', false))->sum('quantity');
        $amilAvailableStock = max(0, $amilPurchased - $amilAllocated);

        return Inertia::render('purchases/purchases', compact('purchases', 'totalQuantity', 'totalValue', 'totalStocks', 'visibleAvailableStock', 'amilAvailableStock'));
    }

    public function create()
    {
        $riceItems = $this->riceItemService->getAllRiceItems();
        return Inertia::render('purchases/purchase-create', compact('riceItems'));
    }

    public function store(PurchaseStoreRequest $request)
    {
        $validatedData = $request->validated();
        $userId = auth()->user()->id;
        $validatedData['created_by'] = $userId;
        $validatedData['date'] = Carbon::parse($validatedData['date'])->format('Y-m-d');

        $this->service->createPurchaseRice($validatedData);

        return redirect()->route('purchases');
    }
}
