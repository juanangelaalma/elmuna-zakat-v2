<?php

namespace App\Contracts;

use App\Models\Fidyah;

interface FidyahServiceInterface
{
    public function createFidyah($data): Fidyah;
}