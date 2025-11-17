<?php

namespace App\Repositories;

use App\Contracts\PurchaseRiceRepositoryInterface;
use App\Models\PurchaseRice;
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
}
