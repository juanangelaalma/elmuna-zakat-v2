<?php

namespace App\Repositories;

use App\Contracts\PurchaseRiceRepositoryInterface;
use App\Models\PurchaseRice;
use App\Models\PurchaseRiceAllocation;
use Illuminate\Support\Collection;

class PurchaseRiceRepository implements PurchaseRiceRepositoryInterface
{
    public function getAll(array $relations = []): Collection
    {
        return PurchaseRice::with($relations)->get();
    }

    public function find(int $id): PurchaseRice
    {
        return PurchaseRice::find($id);
    }

    public function create(array $data): PurchaseRice
    {
        return PurchaseRice::create($data);
    }

    public function update(PurchaseRice $purchaseRice, array $data): PurchaseRice
    {
        $purchaseRice->update($data);
        return $purchaseRice;
    }

    public function delete(PurchaseRice $purchaseRice): bool
    {
        return $purchaseRice->delete();
    }

    public function getAvailablePurchaseRices(): Collection
    {
        return PurchaseRice::select('purchase_rices.id', 'purchase_rices.quantity', 'purchase_rices.price_per_kg')
            ->leftJoin('purchase_rice_allocations', 'purchase_rices.id', 'purchase_rice_allocations.purchase_rice_id')
            ->selectRaw('purchase_rices.quantity - COALESCE(SUM(purchase_rice_allocations.quantity), 0) as remaining_quantity')
            ->groupBy('purchase_rices.id', 'purchase_rices.quantity', 'purchase_rices.price_per_kg')
            ->havingRaw('remaining_quantity > 0')
            ->orderBy('purchase_rices.created_at', 'desc')
            ->get();
    }

    public function decrementPurchaseRiceQuantity(PurchaseRice $purchaseRice, $riceSale, $quantity): void
    {
        PurchaseRiceAllocation::create([
            'purchase_rice_id' => $purchaseRice->id,
            'rice_sales_id' => $riceSale->id,
            'quantity' => $quantity,
        ]);
    }
}
