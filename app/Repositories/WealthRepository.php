<?php

namespace App\Repositories;

use App\Models\Wealth;
use App\Contracts\WealthRepositoryInterface;

class WealthRepository implements WealthRepositoryInterface
{
    public function create($data): Wealth
    {
        return Wealth::create($data);
    }
}
