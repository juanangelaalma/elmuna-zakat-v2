<?php

namespace App\Services;

use App\Contracts\ShiftHandoverRepositoryInterface;
use App\Contracts\ShiftHandoverServiceInterface;
use App\Models\ShiftHandover;
use Illuminate\Support\Collection;

class ShiftHandoverService implements ShiftHandoverServiceInterface
{
    public function __construct(
        private ShiftHandoverRepositoryInterface $repository
    ) {}

    public function getUnsettledTransactions(): Collection
    {
        return $this->repository->getUnsettledTransactions();
    }

    public function createHandover(array $data, array $transactionIds): ShiftHandover
    {
        // Calculate the exact sums from the selected transactions
        $details = \App\Models\TransactionDetail::whereIn('transaction_id', $transactionIds)
            ->whereNull('deleted_at')
            ->get();
            
        $totals = [
            'total_rice_sale_amount'  => 0,
            'total_wealth_amount'     => 0,
            'total_fidyah_amount'     => 0,
            'total_donation_amount'   => 0,
            'total_rice_quantity'     => 0,
            'total_fidyah_quantity'   => 0,
            'total_donation_quantity' => 0,
        ];

        foreach ($details as $detail) {
            switch ($detail->type) {
                case \App\Constants\TransactionItemType::RICE_SALES:
                    $totals['total_rice_sale_amount'] += $detail->riceSale?->amount ?? 0;
                    break;
                case \App\Constants\TransactionItemType::WEALTH:
                    $totals['total_wealth_amount'] += $detail->wealth?->amount ?? 0;
                    break;
                case \App\Constants\TransactionItemType::FIDYAH:
                    $fidyah = $detail->fidyah;
                    if ($fidyah) {
                        $totals['total_fidyah_amount'] += $fidyah->amount ?? 0;
                        if ($fidyah->fidyah_type === 'rice') {
                            $totals['total_fidyah_quantity'] += $fidyah->quantity ?? 0;
                        }
                    }
                    break;
                case \App\Constants\TransactionItemType::DONATION:
                    $donation = $detail->donation;
                    if ($donation) {
                        $totals['total_donation_amount'] += $donation->amount ?? 0;
                        if ($donation->donation_type === 'rice') {
                            $totals['total_donation_quantity'] += $donation->quantity ?? 0;
                        }
                    }
                    break;
                case \App\Constants\TransactionItemType::RICE:
                    $totals['total_rice_quantity'] += $detail->rice?->quantity ?? 0;
                    break;
            }
        }

        $handoverData = array_merge($data, $totals);

        return $this->repository->createHandover($handoverData, $transactionIds);
    }

    public function getList(): Collection
    {
        return $this->repository->getList();
    }

    public function getById(int $id): ?array
    {
        return $this->repository->getById($id);
    }
}
