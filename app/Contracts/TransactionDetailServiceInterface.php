<?php

namespace App\Contracts;

use App\Models\TransactionDetail;

interface TransactionDetailServiceInterface
{
    public function createTransactionDetail(array $data): TransactionDetail;
}