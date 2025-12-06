<?php

namespace App\Contracts;

use App\Models\Donation;

interface DonationRepositoryInterface
{
    public function create($data): Donation;
}