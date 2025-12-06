<?php

namespace App\Services;

use App\Models\Wealth;
use App\Contracts\WealthServiceInterface;
use App\Contracts\WealthRepositoryInterface;

class WealthService implements WealthServiceInterface
{
    public function __construct(
        private WealthRepositoryInterface $repository,
    ) {}

    public function createWealth($data): Wealth
    {
        return $this->repository->create($data);
    }
}
