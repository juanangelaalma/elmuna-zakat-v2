<?php

namespace App\Repositories;

use App\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\DTO\TransactionDTO;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\RiceSale;
use App\Models\PurchaseRiceAllocation;
use App\Constants\TransactionItemType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection as SupportCollection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getList(): Collection
    {
        return Transaction::select($this->getTransactionFields())
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

    /**
     * Get the fields to select for transaction list.
     * Menggunakan correlated subquery per tipe agar nilai tidak terhitung ganda
     * akibat multiple JOIN yang mengalikan baris (row multiplication).
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
     * Get the DB expression for calculating total transaction amount.
     * Menggunakan subquery per tipe agar tidak terjadi double-count.
     */
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

    /**
     * Get the DB expression for calculating total transaction quantity.
     * Menggunakan subquery per tipe agar tidak terjadi double-count.
     */
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
        return Transaction::withTrashed()->orderBy('created_at', 'desc')->first();
    }

    public function getById(int $id): ?array
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return null;
        }

        $details = DB::table('transaction_details as td')
            ->where('td.transaction_id', $id)
            ->whereNull('td.deleted_at')
            ->leftJoin('rice_sales as rs', function ($join) {
                $join->on('td.id', '=', 'rs.transaction_detail_id')
                    ->where('td.type', TransactionItemType::RICE_SALES);
            })
            ->leftJoin('rices as r', function ($join) {
                $join->on('td.id', '=', 'r.transaction_detail_id')
                    ->where('td.type', TransactionItemType::RICE);
            })
            ->leftJoin('donations as d', function ($join) {
                $join->on('td.id', '=', 'd.transaction_detail_id')
                    ->where('td.type', TransactionItemType::DONATION);
            })
            ->leftJoin('fidyahs as f', function ($join) {
                $join->on('td.id', '=', 'f.transaction_detail_id')
                    ->where('td.type', TransactionItemType::FIDYAH);
            })
            ->leftJoin('wealths as w', function ($join) {
                $join->on('td.id', '=', 'w.transaction_detail_id')
                    ->where('td.type', TransactionItemType::WEALTH);
            })
            ->select([
                'td.id',
                'td.giver_name as customer',
                'td.type as item_type',
                DB::raw('COALESCE(rs.quantity, r.quantity, d.quantity, f.quantity) as quantity'),
                DB::raw('COALESCE(rs.amount, d.amount, f.amount, w.amount) as amount'),
                DB::raw('d.donation_type'),
                DB::raw('f.fidyah_type'),
            ])
            ->get()
            ->map(function ($detail) {
                $item = [
                    'customer' => $detail->customer,
                    'item_type' => $detail->item_type,
                    'detail' => []
                ];

                switch ($detail->item_type) {
                    case TransactionItemType::RICE_SALES:
                        $item['detail'] = [
                            'quantity' => $detail->quantity,
                            'amount' => $detail->amount,
                        ];
                        break;
                    case TransactionItemType::RICE:
                        $item['detail'] = [
                            'quantity' => $detail->quantity,
                        ];
                        break;
                    case TransactionItemType::DONATION:
                        $item['detail'] = [
                            'donation_type' => $detail->donation_type,
                            'quantity' => $detail->donation_type === 'rice' ? $detail->quantity : null,
                            'amount' => $detail->donation_type === 'money' ? $detail->amount : null,
                        ];
                        break;
                    case TransactionItemType::FIDYAH:
                        $item['detail'] = [
                            'fidyah_type' => $detail->fidyah_type,
                            'quantity' => $detail->fidyah_type === 'rice' ? $detail->quantity : null,
                            'amount' => $detail->fidyah_type === 'money' ? $detail->amount : null,
                        ];
                        break;
                    case TransactionItemType::WEALTH:
                        $item['detail'] = [
                            'amount' => $detail->amount,
                        ];
                        break;
                }

                return $item;
            })
            ->toArray();

        return [
            'id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number,
            'date' => $transaction->date,
            'customer' => $transaction->customer,
            'address' => $transaction->address,
            'wa_number' => $transaction->wa_number,
            'is_wa_sent' => $transaction->is_wa_sent,
            'officer_name' => $transaction->officer_name,
            'items' => $details,
        ];
    }

    public function getRiceSales(): SupportCollection
    {
        return DB::table('transaction_details as td')
            ->join('rice_sales as rs', 'td.id', '=', 'rs.transaction_detail_id')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->whereNull('t.deleted_at')
            ->whereNull('td.deleted_at')
            ->where('td.type', TransactionItemType::RICE_SALES)
            ->select([
                'td.id',
                'td.giver_name as customer',
                'rs.quantity',
                'rs.amount as total',
                't.date',
                't.transaction_number',
            ])
            ->orderBy('t.date', 'desc')
            ->get();
    }

    public function getRice(): SupportCollection
    {
        return DB::table('transaction_details as td')
            ->join('rices as r', 'td.id', '=', 'r.transaction_detail_id')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->whereNull('t.deleted_at')
            ->whereNull('td.deleted_at')
            ->where('td.type', TransactionItemType::RICE)
            ->select([
                'td.id',
                'td.giver_name as customer',
                'r.quantity',
                't.date',
                't.transaction_number',
            ])
            ->orderBy('t.date', 'desc')
            ->get();
    }

    public function getDonations(): SupportCollection
    {
        return DB::table('transaction_details as td')
            ->join('donations as d', 'td.id', '=', 'd.transaction_detail_id')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->whereNull('t.deleted_at')
            ->whereNull('td.deleted_at')
            ->where('td.type', TransactionItemType::DONATION)
            ->select([
                'td.id',
                'td.giver_name as customer',
                'd.donation_type as type',
                'd.quantity',
                'd.amount',
                't.date',
                't.transaction_number',
            ])
            ->orderBy('t.date', 'desc')
            ->get();
    }

    public function getFidyah(): SupportCollection
    {
        return DB::table('transaction_details as td')
            ->join('fidyahs as f', 'td.id', '=', 'f.transaction_detail_id')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->whereNull('t.deleted_at')
            ->whereNull('td.deleted_at')
            ->where('td.type', TransactionItemType::FIDYAH)
            ->select([
                'td.id',
                'td.giver_name as customer',
                'f.fidyah_type as type',
                'f.quantity',
                'f.amount',
                't.date',
                't.transaction_number',
            ])
            ->orderBy('t.date', 'desc')
            ->get();
    }

    public function getWealths(): SupportCollection
    {
        return DB::table('transaction_details as td')
            ->join('wealths as w', 'td.id', '=', 'w.transaction_detail_id')
            ->join('transactions as t', 'td.transaction_id', '=', 't.id')
            ->whereNull('t.deleted_at')
            ->whereNull('td.deleted_at')
            ->where('td.type', TransactionItemType::WEALTH)
            ->select([
                'td.id',
                'td.giver_name as customer',
                'w.amount',
                't.date',
                't.transaction_number',
            ])
            ->orderBy('t.date', 'desc')
            ->get();
    }

    public function deleteTransaction(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);

            // Load all active transaction details
            $details = TransactionDetail::where('transaction_id', $id)->get();

            foreach ($details as $detail) {
                if ($detail->type === TransactionItemType::RICE_SALES) {
                    // Find the RiceSale linked to this detail
                    $riceSale = RiceSale::where('transaction_detail_id', $detail->id)->first();

                    if ($riceSale) {
                        // Delete all purchase allocations to restore stock
                        PurchaseRiceAllocation::where('rice_sales_id', $riceSale->id)->delete();
                        // Delete the RiceSale record itself
                        $riceSale->delete();
                    }
                }

                // Soft delete the detail
                $detail->delete();
            }

            // Soft delete the transaction header
            $transaction->delete();

            return true;
        });
    }

    public function getTrashedList(): SupportCollection
    {
        return Transaction::onlyTrashed()
            ->select(
                'transactions.id',
                'transactions.transaction_number',
                'transactions.date',
                'transactions.customer',
                'transactions.officer_name',
                'transactions.deleted_at'
            )
            ->orderBy('transactions.deleted_at', 'desc')
            ->get();
    }

    public function restoreTransaction(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::withTrashed()->findOrFail($id);
            $transaction->restore();

            // Restore all soft-deleted details for this transaction
            TransactionDetail::withTrashed()
                ->where('transaction_id', $id)
                ->restore();

            // Re-create PurchaseRiceAllocations for RICE_SALES items
            $riceDetails = TransactionDetail::where('transaction_id', $id)
                ->where('type', TransactionItemType::RICE_SALES)
                ->get();

            foreach ($riceDetails as $detail) {
                $riceSale = RiceSale::where('transaction_detail_id', $detail->id)->first();

                if ($riceSale) {
                    $this->reallocateRiceSale($riceSale);
                }
            }

            return true;
        });
    }

    public function getByIdWithTrashed(int $id): ?array
    {
        $transaction = Transaction::withTrashed()->find($id);

        if (!$transaction) {
            return null;
        }

        $details = DB::table('transaction_details as td')
            ->where('td.transaction_id', $id)
            ->whereNull('td.deleted_at')
            ->leftJoin('rice_sales as rs', function ($join) {
                $join->on('td.id', '=', 'rs.transaction_detail_id')
                    ->where('td.type', TransactionItemType::RICE_SALES);
            })
            ->leftJoin('rices as r', function ($join) {
                $join->on('td.id', '=', 'r.transaction_detail_id')
                    ->where('td.type', TransactionItemType::RICE);
            })
            ->leftJoin('donations as d', function ($join) {
                $join->on('td.id', '=', 'd.transaction_detail_id')
                    ->where('td.type', TransactionItemType::DONATION);
            })
            ->leftJoin('fidyahs as f', function ($join) {
                $join->on('td.id', '=', 'f.transaction_detail_id')
                    ->where('td.type', TransactionItemType::FIDYAH);
            })
            ->leftJoin('wealths as w', function ($join) {
                $join->on('td.id', '=', 'w.transaction_detail_id')
                    ->where('td.type', TransactionItemType::WEALTH);
            })
            ->select([
                'td.id',
                'td.giver_name as customer',
                'td.type as item_type',
                DB::raw('COALESCE(rs.quantity, r.quantity, d.quantity, f.quantity) as quantity'),
                DB::raw('COALESCE(rs.amount, d.amount, f.amount, w.amount) as amount'),
                DB::raw('d.donation_type'),
                DB::raw('f.fidyah_type'),
            ])
            ->get()
            ->map(function ($detail) {
                $item = [
                    'customer'  => $detail->customer,
                    'item_type' => $detail->item_type,
                    'detail'    => [],
                ];

                switch ($detail->item_type) {
                    case TransactionItemType::RICE_SALES:
                        $item['detail'] = ['quantity' => $detail->quantity, 'amount' => $detail->amount];
                        break;
                    case TransactionItemType::RICE:
                        $item['detail'] = ['quantity' => $detail->quantity];
                        break;
                    case TransactionItemType::DONATION:
                        $item['detail'] = [
                            'donation_type' => $detail->donation_type,
                            'quantity'      => $detail->donation_type === 'rice' ? $detail->quantity : null,
                            'amount'        => $detail->donation_type === 'money' ? $detail->amount : null,
                        ];
                        break;
                    case TransactionItemType::FIDYAH:
                        $item['detail'] = [
                            'fidyah_type' => $detail->fidyah_type,
                            'quantity'    => $detail->fidyah_type === 'rice' ? $detail->quantity : null,
                            'amount'      => $detail->fidyah_type === 'money' ? $detail->amount : null,
                        ];
                        break;
                    case TransactionItemType::WEALTH:
                        $item['detail'] = ['amount' => $detail->amount];
                        break;
                }

                return $item;
            })
            ->toArray();

        return [
            'id'                 => $transaction->id,
            'transaction_number' => $transaction->transaction_number,
            'date'               => $transaction->date,
            'customer'           => $transaction->customer,
            'address'            => $transaction->address,
            'wa_number'          => $transaction->wa_number,
            'is_wa_sent'         => $transaction->is_wa_sent,
            'officer_name'       => $transaction->officer_name,
            'items'              => $details,
        ];
    }

    public function alreadyHandedOver(int $id): bool
    {
        return Transaction::find($id)->shiftHandover()->exists();
    }

    public function getWaList(): Collection
    {
        return Transaction::whereNotNull('wa_number')
            ->where('wa_number', '!=', '')
            ->select([
                'id',
                'transaction_number',
                'date',
                'customer',
                'wa_number',
                'officer_name',
                'is_wa_sent',
                'created_at',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Re-allocate stock from available purchase rices for a given rice sale (used on restore).
     */
    private function reallocateRiceSale(RiceSale $riceSale): void
    {
        $availablePurchaseRices = DB::table('purchase_rices')
            ->select('purchase_rices.id', 'purchase_rices.quantity', DB::raw(
                'purchase_rices.quantity - COALESCE(SUM(purchase_rice_allocations.quantity), 0) as remaining_quantity'
            ))
            ->leftJoin('purchase_rice_allocations', 'purchase_rices.id', '=', 'purchase_rice_allocations.purchase_rice_id')
            ->groupBy('purchase_rices.id', 'purchase_rices.quantity')
            ->havingRaw('remaining_quantity > 0')
            ->orderBy('purchase_rices.created_at', 'desc')
            ->get();

        $needed    = $riceSale->quantity;
        $allocated = 0;

        foreach ($availablePurchaseRices as $pr) {
            if ($allocated >= $needed) break;

            $chunk = min($needed - $allocated, $pr->remaining_quantity);

            PurchaseRiceAllocation::create([
                'purchase_rice_id' => $pr->id,
                'rice_sales_id'    => $riceSale->id,
                'quantity'         => $chunk,
            ]);

            $allocated += $chunk;
        }
    }
}
