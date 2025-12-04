<?php

namespace App\Contracts;

interface TransactionItemInterface
{
    public function process($transactionDetailId): void;
}