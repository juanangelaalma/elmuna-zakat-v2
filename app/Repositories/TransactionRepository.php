<?php

namespace App\Repositories;

use App\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\DTO\TransactionDTO;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getList(): Collection
    {
        return Transaction::all();
    }

    // TODO: implement createTransaction
    public function createTransaction(TransactionDTO $transaction): Transaction
    {
        return Transaction::create(
            [
                'transaction_number' => $transaction->transaction_number,
                'date' => $transaction->date,
                'customer' => $transaction->customer,
                'address' => $transaction->address,
                'wa_number' => $transaction->wa_number,
                'officer_name' => $transaction->officer_name,
                'created_by' => $transaction->created_by,
            ]
        );
    }

    public function getLatestTransaction(): ?Transaction
    {
        return Transaction::orderBy('created_at', 'desc')->first();
    }
}
