<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ShiftHandoverServiceInterface
{
    public function getUnsettledTransactions(): Collection;
    public function createHandover(array $data, array $transactionIds): \App\Models\ShiftHandover;
    public function getList(): Collection;
    public function getById(int $id): ?array;
}
