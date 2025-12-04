<?php

namespace App\Contracts;

use App\Models\PurchaseRice;
use Illuminate\Support\Collection;

interface PurchaseRiceRepositoryInterface
{
    public function getAll(array $relations = []): Collection;
    public function find(int $id): PurchaseRice;
    public function create(array $data): PurchaseRice;
    public function update(PurchaseRice $purchaseRice, array $data): PurchaseRice;
    public function delete(PurchaseRice $purchaseRice): bool;
    public function getAvailablePurchaseRices(): Collection;
    public function decrementPurchaseRiceQuantity(PurchaseRice $purchaseRice, $riceSale, $quantity): void;
}
