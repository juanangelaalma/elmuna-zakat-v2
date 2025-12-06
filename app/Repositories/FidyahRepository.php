<?php

namespace App\Repositories;

use App\Models\Fidyah;
use App\Contracts\FidyahRepositoryInterface;

class FidyahRepository implements FidyahRepositoryInterface
{
    public function create($data): Fidyah
    {
        return Fidyah::create($data);
    }
}
