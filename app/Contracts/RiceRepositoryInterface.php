<?php

namespace App\Contracts;

use App\Models\Rice;

interface RiceRepositoryInterface
{
    public function create($data): Rice;
}