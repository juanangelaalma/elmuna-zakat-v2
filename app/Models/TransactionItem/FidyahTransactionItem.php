<?php

namespace App\Models\TransactionItem;

use App\Contracts\FidyahServiceInterface;
use App\Contracts\TransactionItemInterface;
use App\Helpers\FidyahHelper;

class FidyahTransactionItem implements TransactionItemInterface
{
    public $item;
    public FidyahServiceInterface $fidyahService;

    public function __construct($item = [])
    {
        $this->item = $item;
        $this->fidyahService = app(FidyahServiceInterface::class);
    }

    public function process($transactionDetail): void
    {
        $this->item['detail']['transaction_detail_id'] = $transactionDetail->id;
        $this->item['detail'] = FidyahHelper::normalizeDetail($this->item['detail']);
        $this->fidyahService->createFidyah($this->item['detail']);
    }
}