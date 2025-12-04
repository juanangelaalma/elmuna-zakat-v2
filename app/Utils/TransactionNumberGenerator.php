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
        $storeCode = 'EM';

        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $lastTransaction = $this->repository->getLatestTransaction();

        $sequenceNumber = 1;
        if ($lastTransaction) {
            $lastSequence = (int)substr($lastTransaction->transaction_number, -4);
            $sequenceNumber = $lastSequence + 1;
        }

        $transactionNumber = $storeCode . '-' . $year . '-' . $month . '-' . str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);

        return $transactionNumber;
    }
}