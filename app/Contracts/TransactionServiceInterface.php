<?php

namespace App\Contracts;

use App\DTO\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function getList(): Collection;
    public function getById(int $id): ?array;
    public function createTransaction(TransactionDTO $transaction): Transaction;
}
