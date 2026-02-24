<?php

namespace App\Models\TransactionItem;

use App\Contracts\FidyahServiceInterface;
use App\Contracts\TransactionItemInterface;

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
        
        // Sanitasi: pastikan kolom yang tidak relevan bernilai null
        if (isset($this->item['detail']['fidyah_type'])) {
            if ($this->item['detail']['fidyah_type'] === 'money') {
                $this->item['detail']['quantity'] = null;
            } elseif ($this->item['detail']['fidyah_type'] === 'rice') {
                $this->item['detail']['amount'] = null;
            }
        }

        $this->fidyahService->createFidyah($this->item['detail']);
    }
}