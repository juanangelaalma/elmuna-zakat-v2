<?php

namespace App\Services;

use App\Contracts\RiceItemServiceInterface;
use App\Contracts\RiceItemRepositoryInterface;
use App\Models\RiceItem;
use Illuminate\Database\Eloquent\Collection;

class RiceItemService implements RiceItemServiceInterface
{
    public function __construct(
        private RiceItemRepositoryInterface $repository,
    ) {}

    public function getAllRiceItems(): Collection
    {
        return $this->repository->getAll();
    }

    public function createRiceItem(array $data): RiceItem
    {
        return $this->repository->create($data);
    }
}
