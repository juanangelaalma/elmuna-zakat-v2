<?php

namespace App\Contracts;

use App\Models\Rice;

interface RiceServiceInterface
{
    public function createRice($data): Rice;
}