<?php

namespace App\Contracts;

use App\Models\Fidyah;

interface FidyahRepositoryInterface
{
    public function create($data): Fidyah;
}