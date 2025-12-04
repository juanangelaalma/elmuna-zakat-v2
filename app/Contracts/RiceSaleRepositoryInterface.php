<?php

namespace App\Contracts;

use App\Models\RiceSale;

interface RiceSaleRepositoryInterface
{
    public function create(array $riceSale): RiceSale;
}