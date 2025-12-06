<?php

namespace App\Contracts;

use App\Models\Wealth;

interface WealthRepositoryInterface
{
    public function create($data): Wealth;
}