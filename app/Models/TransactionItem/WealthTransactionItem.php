<?php

namespace App\Models\TransactionItem;
namespace App\Contracts\TransactionItemInterface;

class WealthTransactionItem implements TransactionItemInterface
{
    public $item;

    public function __construct($item = [])
    {
        $this->item = $item;
    }

    public function process(): void
    {
        dd($this->item);
    }
}