<?php

namespace App\Services;

use App\Contracts\RiceSaleRepositoryInterface;
use App\Contracts\RiceSaleServiceInterface;
use App\Models\RiceSale;

class RiceSaleService implements RiceSaleServiceInterface
{
    public function __construct(
        private RiceSaleRepositoryInterface $repository
    ) {}

    public function createRiceSaleService(array $riceSaleService): RiceSale
    {
        return $this->repository->create($riceSaleService);
    }
}