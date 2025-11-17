<?php

namespace App\Contracts;

use App\Models\PurchaseRice;
use Illuminate\Support\Collection;

interface PurchaseRiceServiceInterface
{
    public function getAllPurchaseRiceWithRiceItem(): Collection;
    public function createPurchaseRice(array $data): PurchaseRice;
    public function updatePurchaseRice(int $id, array $data): PurchaseRice;
    public function deletePurchaseRice(int $id): bool;
}
