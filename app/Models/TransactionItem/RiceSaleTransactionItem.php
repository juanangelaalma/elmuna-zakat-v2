<?php

namespace App\Models\TransactionItem;

use App\Contracts\PurchaseRiceServiceInterface;
use App\Contracts\RiceSaleServiceInterface;
use App\Contracts\TransactionItemInterface;

class RiceSaleTransactionItem implements TransactionItemInterface
{
    protected $purchaseRiceService;
    protected $riceSaleService;
    public $item;

    public function __construct(PurchaseRiceServiceInterface $purchaseRiceService, RiceSaleServiceInterface $riceSaleService, $item = [])
    {
        $this->purchaseRiceService = $purchaseRiceService;
        $this->riceSaleService = $riceSaleService;
        $this->item = $item;
    }

    public function process($transactionDetail): void
    {
        $riceSale = $this->riceSaleService->createRiceSaleService([
            'transaction_detail_id' => $transactionDetail->id,
            'amount' => $this->item['detail']['amount'],
            'quantity' => $this->item['detail']['quantity'],
            'total_price' => $this->item['detail']['amount'],
        ]);

        $this->purchaseRiceService->allocatePurchaseRiceToRiceSale($riceSale);
    }
}