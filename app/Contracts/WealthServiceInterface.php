<?php

namespace App\Contracts;

use App\Models\Wealth;

interface WealthServiceInterface
{
    public function createWealth($data): Wealth;
}