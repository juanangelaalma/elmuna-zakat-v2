<?php

namespace App\Contracts;

use App\Models\TransactionDetail;

interface TransactionDetailRepositoryInterface
{
    public function createTransactionDetail(array $transactionDetail): TransactionDetail;
}