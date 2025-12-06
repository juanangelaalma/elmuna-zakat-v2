<?php

namespace App\Services;

use App\Contracts\RiceRepositoryInterface;
use App\Contracts\RiceServiceInterface;
use App\Models\Rice;

class RiceService implements RiceServiceInterface
{
    public function __construct(
        private RiceRepositoryInterface $repository,
    ) {}

    public function createRice($data): Rice
    {
        return $this->repository->create($data);
    }
}