<?php

namespace App\Contracts;

use App\DTO\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface TransactionServiceInterface
{
    public function getList(): Collection;
    public function getById(int $id): ?array;
    public function createTransaction(TransactionDTO $transaction): Transaction;
    public function getRiceSales(): SupportCollection;
    public function getRice(): SupportCollection;
    public function getDonations(): SupportCollection;
    public function getFidyah(): SupportCollection;
    public function getWealths(): SupportCollection;
}
