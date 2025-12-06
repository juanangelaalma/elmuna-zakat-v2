<?php

namespace App\Models\TransactionItem;
use App\Contracts\TransactionItemInterface;
use App\Contracts\WealthServiceInterface;

class WealthTransactionItem implements TransactionItemInterface
{
    public $item;
    public WealthServiceInterface $wealthService;

    public function __construct($item = [])
    {
        $this->item = $item;
        $this->wealthService = app(WealthServiceInterface::class);
    }

    public function process($transactionDetail): void
    {
        $this->item['detail']['transaction_detail_id'] = $transactionDetail->id;
        $this->wealthService->createWealth($this->item['detail']);
    }
}