<?php

namespace App\Repositories;

use App\Models\Rice;
use App\Contracts\RiceRepositoryInterface;

class RiceRepository implements RiceRepositoryInterface
{
    public function create($data): Rice
    {
        return Rice::create($data);
    }
}