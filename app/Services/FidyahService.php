<?php

namespace App\Services;

use App\Models\Fidyah;
use App\Contracts\FidyahServiceInterface;
use App\Contracts\FidyahRepositoryInterface;

class FidyahService implements FidyahServiceInterface
{
    public function __construct(
        private FidyahRepositoryInterface $repository,
    ) {}

    public function createFidyah($data): Fidyah
    {
        return $this->repository->create($data);
    }
}
