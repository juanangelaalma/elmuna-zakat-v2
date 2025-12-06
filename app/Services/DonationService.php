<?php

namespace App\Services;

use App\Models\Donation;
use App\Contracts\DonationServiceInterface;
use App\Contracts\DonationRepositoryInterface;

class DonationService implements DonationServiceInterface
{
    public function __construct(
        private DonationRepositoryInterface $repository,
    ) {}

    public function createDonation($data): Donation
    {
        return $this->repository->create($data);
    }
}
