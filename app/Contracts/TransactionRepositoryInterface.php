<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\DTO\TransactionDTO;
use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function getList(): Collection;
    public function getById(int $id): ?array;
    public function createTransaction(TransactionDTO $transaction): Transaction;
}
