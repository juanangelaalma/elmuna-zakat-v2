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
                $this->item['detail']['amount'] = $this->item['detail']['amount'] * $this->item['detail']['day_count'];
            } elseif ($this->item['detail']['fidyah_type'] === 'rice') {
                $this->item['detail']['amount'] = null;
                $this->item['detail']['quantity'] = $this->item['detail']['quantity'] * $this->item['detail']['day_count'];
            }
        }

        $this->fidyahService->createFidyah($this->item['detail']);
    }
}