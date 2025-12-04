<?php

namespace App\Repositories;

use App\Contracts\RiceSaleRepositoryInterface;
use App\Models\RiceSale;

class RiceSaleRepository implements RiceSaleRepositoryInterface
{
    public function create(array $riceSale): RiceSale
    {
        return RiceSale::create($riceSale);
    }
}