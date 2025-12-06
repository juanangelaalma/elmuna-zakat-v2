<?php

namespace App\Repositories;

use App\Models\Donation;
use App\Contracts\DonationRepositoryInterface;

class DonationRepository implements DonationRepositoryInterface
{
    public function create($data): Donation
    {
        return Donation::create($data);
    }
}
