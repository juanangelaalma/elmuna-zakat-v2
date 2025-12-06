<?php

namespace App\Models\TransactionItem;

use App\Contracts\TransactionItemInterface;
use App\Contracts\RiceServiceInterface;

class RiceTransactionItem implements TransactionItemInterface
{
    public $item;
    public RiceServiceInterface $riceService;

    public function __construct($item = [])
    {
        $this->item = $item;
        $this->riceService = app(RiceServiceInterface::class);
    }

    public function process($transactionDetail): void
    {
        $this->item['detail']['transaction_detail_id'] = $transactionDetail->id;
        $this->riceService->createRice($this->item['detail']);
    }
}