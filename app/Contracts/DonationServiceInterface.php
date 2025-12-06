<?php

namespace App\Contracts;

use App\Models\Donation;

interface DonationServiceInterface
{
    public function createDonation($data): Donation;
}