<?php

namespace App\Utils;

use Illuminate\Support\Carbon;
use App\Contracts\TransactionRepositoryInterface;

class TransactionNumberGenerator
{
    public function __construct(
        private TransactionRepositoryInterface $repository
    ) {}

    public function generate()
    {
        $storeCode = 'UPZ';

        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $lastTransaction = $this->repository->getLatestTransaction();

        $sequenceNumber = 1;
        if ($lastTransaction) {
            $parts = explode('-', $lastTransaction->transaction_number);
            $lastSequence = (int)($parts[1] ?? 0);
            $sequenceNumber = $lastSequence + 1;
        }

        $transactionNumber = $storeCode . '-' . str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT) . '-' . $month . '-' . $year;

        return $transactionNumber;
    }
}