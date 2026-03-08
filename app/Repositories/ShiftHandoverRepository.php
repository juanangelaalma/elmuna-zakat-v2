<?php

namespace App\Repositories;

use App\Contracts\ShiftHandoverRepositoryInterface;
use App\Models\ShiftHandover;
use App\Models\Transaction;
use App\Constants\TransactionItemType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShiftHandoverRepository implements ShiftHandoverRepositoryInterface
{
    public function getUnsettledTransactions(): Collection
    {
        return Transaction::select($this->getTransactionFields())
            ->whereNull('shift_handover_id')
            ->orderBy('created_at', 'desc')
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

    private function getTransactionFields(): array
    {
        return [
            'transactions.id',
            'transactions.shift_handover_id',
            'transactions.transaction_number',
            'transactions.date',
            'transactions.created_at',
            'transactions.customer',
            'transactions.wa_number',
            'transactions.officer_name',
            $this->getTotalAmountExpression(),
            $this->getTotalQuantityExpression(),
        ];
    }

    private function getTotalAmountExpression()
    {
        return DB::raw('
            COALESCE((
                SELECT SUM(rs.amount)
                FROM transaction_details td
                JOIN rice_sales rs ON rs.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::RICE_SALES . '\'
                  AND td.deleted_at IS NULL
            ), 0) +
            COALESCE((
                SELECT SUM(d.amount)
                FROM transaction_details td
                JOIN donations d ON d.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::DONATION . '\'
                  AND td.deleted_at IS NULL
            ), 0) +
            COALESCE((
                SELECT SUM(f.amount)
                FROM transaction_details td
                JOIN fidyahs f ON f.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::FIDYAH . '\'
                  AND td.deleted_at IS NULL
            ), 0) +
            COALESCE((
                SELECT SUM(w.amount)
                FROM transaction_details td
                JOIN wealths w ON w.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::WEALTH . '\'
                  AND td.deleted_at IS NULL
            ), 0)
            AS total_transaction_amount
        ');
    }

    private function getTotalQuantityExpression()
    {
        return DB::raw('
            COALESCE((
                SELECT SUM(r.quantity)
                FROM transaction_details td
                JOIN rices r ON r.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::RICE . '\'
                  AND td.deleted_at IS NULL
            ), 0) +
            COALESCE((
                SELECT SUM(d.quantity)
                FROM transaction_details td
                JOIN donations d ON d.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::DONATION . '\'
                  AND td.deleted_at IS NULL
            ), 0) +
            COALESCE((
                SELECT SUM(f.quantity)
                FROM transaction_details td
                JOIN fidyahs f ON f.transaction_detail_id = td.id
                WHERE td.transaction_id = transactions.id
                  AND td.type = \'' . TransactionItemType::FIDYAH . '\'
                  AND td.deleted_at IS NULL
            ), 0)
            AS total_transaction_quantity
        ');
    }

    public function createHandover(array $data, array $transactionIds): ShiftHandover
    {
        return DB::transaction(function () use ($data, $transactionIds) {
            $handover = ShiftHandover::create($data);

            Transaction::whereIn('id', $transactionIds)
                ->whereNull('shift_handover_id') // Ensure they aren't already handed over
                ->update(['shift_handover_id' => $handover->id]);

            return $handover;
        });
    }

    public function getList(): Collection
    {
        return ShiftHandover::orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): ?array
    {
        $handover = ShiftHandover::with(['transactions' => function ($query) {
            $query->select($this->getTransactionFields())
                  ->groupBy(
                      'transactions.id',
                      'transactions.transaction_number',
                      'transactions.date',
                      'transactions.created_at',
                      'transactions.customer',
                      'transactions.wa_number',
                      'transactions.officer_name',
                      'transactions.shift_handover_id'
                  );
        }])->find($id);

        if (!$handover) {
            return null;
        }

        return $handover->toArray();
    }
}
