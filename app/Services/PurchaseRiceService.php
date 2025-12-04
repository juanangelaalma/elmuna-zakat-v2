<?php

namespace App\Services;

use App\Contracts\PurchaseRiceRepositoryInterface;
use App\Contracts\PurchaseRiceServiceInterface;
use App\Models\PurchaseRice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class PurchaseRiceService implements PurchaseRiceServiceInterface
{
    public function __construct(
        private PurchaseRiceRepositoryInterface $repository
    ) {}

    public function getAllPurchaseRiceWithRiceItem(): Collection
    {
        return $this->repository->getAll(['riceItem']);
    }

    public function createPurchaseRice(array $data): PurchaseRice
    {
        return $this->repository->create($data);
    }

    public function updatePurchaseRice(int $id, array $data): PurchaseRice
    {
        $purchaseRice = $this->repository->find($id);

        if (!$purchaseRice) {
            throw new ModelNotFoundException();
        }

        return $this->repository->update($purchaseRice, $data);
    }

    public function deletePurchaseRice(int $id): bool
    {
        $purchaseRice = $this->repository->find($id);

        if (!$purchaseRice) {
            throw new ModelNotFoundException();
        }

        return $this->repository->delete($purchaseRice);
    }

    public function allocatePurchaseRiceToRiceSale($riceSale)
    {
        $allocatedQuantity = 0;
        $neededQuantity = $riceSale->quantity;

        $availablePurchaseRices = $this->repository->getAvailablePurchaseRices();

        foreach($availablePurchaseRices as $purchaseRice) {
            if ($allocatedQuantity >= $neededQuantity) {
                break;
            }

            $remainingQuantity = $purchaseRice->remaining_quantity;
            $allocationQuantity = min($neededQuantity - $allocatedQuantity, $remainingQuantity);

            $this->repository->decrementPurchaseRiceQuantity($purchaseRice, $riceSale, $allocationQuantity);

            $allocatedQuantity += $allocationQuantity;
        }

        if ($allocatedQuantity < $neededQuantity) {
            throw new \Exception('Stok tidak cukup untuk memenuhi transaksi.');
        }
    }
}
