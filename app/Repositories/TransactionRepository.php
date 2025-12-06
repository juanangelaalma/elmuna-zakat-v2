<?php

namespace App\Repositories;

use App\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\DTO\TransactionDTO;
use App\Models\Transaction;
use App\Constants\TransactionItemType;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getList(): Collection
    {
        return Transaction::select($this->getTransactionFields())
            ->join('transaction_details as td', 'transactions.id', '=', 'td.transaction_id')
            ->tap(fn($query) => $this->applyTransactionDetailJoins($query))
            ->groupBy(
                'transactions.id',
                'transactions.transaction_number',
                'transactions.date',
                'transactions.customer',
                'transactions.wa_number',
                'transactions.officer_name'
            )
            ->get();
    }

    /**
     * Get the fields to select for transaction list
     */
    private function getTransactionFields(): array
    {
        return [
            'transactions.id',
            'transactions.transaction_number',
            'transactions.date',
            'transactions.customer',
            'transactions.wa_number',
            'transactions.officer_name',
            $this->getTotalAmountExpression(),
            $this->getTotalQuantityExpression(),
        ];
    }

    /**
     * Get the DB expression for calculating total transaction amount
     */
    private function getTotalAmountExpression()
    {
        return DB::raw('
            COALESCE(SUM(rs.amount), 0) +
            COALESCE(SUM(d.amount), 0) +
            COALESCE(SUM(f.amount), 0) +
            COALESCE(SUM(w.amount), 0) AS total_transaction_amount
        ');
    }

    /**
     * Get the DB expression for calculating total transaction quantity
     */
    private function getTotalQuantityExpression()
    {
        return DB::raw('
            COALESCE(SUM(r.quantity), 0) +
            COALESCE(SUM(d.quantity), 0) +
            COALESCE(SUM(f.quantity), 0) AS total_transaction_quantity
        ');
    }

    /**
     * Apply all transaction detail joins to the query
     */
    private function applyTransactionDetailJoins($query): void
    {
        $joins = [
            ['rice_sales', 'rs', TransactionItemType::RICE_SALES],
            ['rices', 'r', TransactionItemType::RICE],
            ['donations', 'd', TransactionItemType::DONATION],
            ['fidyahs', 'f', TransactionItemType::FIDYAH],
            ['wealths', 'w', TransactionItemType::WEALTH],
        ];

        foreach ($joins as [$table, $alias, $type]) {
            $query->leftJoin("{$table} as {$alias}", function ($join) use ($alias, $type) {
                $join->on('td.id', '=', "{$alias}.transaction_detail_id")
                    ->where('td.type', $type);
            });
        }
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
