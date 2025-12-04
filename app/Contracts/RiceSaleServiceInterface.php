<?php

namespace App\Contracts;
use App\Models\RiceSale;

interface RiceSaleServiceInterface
{
    public function createRiceSaleService(array $riceSaleService): RiceSale;
}