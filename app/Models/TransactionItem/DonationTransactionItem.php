<?php

namespace App\Models\TransactionItem;

use App\Contracts\TransactionItemInterface;
use App\Contracts\DonationServiceInterface;

class DonationTransactionItem implements TransactionItemInterface
{
    public $item;
    public DonationServiceInterface $donationService;

    public function __construct($item = [])
    {
        $this->item = $item;
        $this->donationService = app(DonationServiceInterface::class);
    }

    public function process($transactionDetail): void
    {
        $this->item['detail']['transaction_detail_id'] = $transactionDetail->id;
        $this->donationService->createDonation($this->item['detail']);
    }
}