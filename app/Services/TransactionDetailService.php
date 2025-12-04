<?php

namespace App\Services;

use App\Contracts\TransactionDetailServiceInterface;
use App\Models\TransactionDetail;
use App\Repositories\TransactionDetailRepository;
use App\Factories\TransactionItemFactory;

class TransactionDetailService implements TransactionDetailServiceInterface
{
    public function __construct(
        private TransactionDetailRepository $transactionDetailRepository
    ) {}

    public function createTransactionDetail(array $data): TransactionDetail
    {
        $transactionDetail = $this->transactionDetailRepository->createTransactionDetail([
            'transaction_id' => $data['transaction_id'],
            'giver_name' => $data['customer'],
            'type' => $data['item_type'],
        ]);

        $factory = new TransactionItemFactory();
        $transactionItem = $factory->create($data);
        $transactionItem->process($transactionDetail);

        return $transactionDetail;
    }
}