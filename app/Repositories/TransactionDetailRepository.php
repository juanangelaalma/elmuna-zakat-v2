<?php

namespace App\Repositories;

use App\Models\TransactionDetail;
use App\Contracts\TransactionDetailRepositoryInterface;

class TransactionDetailRepository implements TransactionDetailRepositoryInterface
{
    public function createTransactionDetail(array $transactionDetail): TransactionDetail
    {
        return TransactionDetail::create($transactionDetail);
    }
}