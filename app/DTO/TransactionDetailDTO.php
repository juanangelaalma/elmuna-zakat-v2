<?php

namespace App\DTO;

use App\Models\Donation;
use App\Models\Fidyah;
use App\Models\Rice;
use App\Models\RiceSale;
use App\Models\Wealth;

class TransactionDetailDTO
{
    public int $id;
    public int $transaction_id;
    public string $giver_name;
    public string $type;
    public Rice | RiceSale | Donation | Fidyah | Wealth $item;

    public function __construct(
        int $id,
        int $transaction_id,
        string $giver_name,
        string $type,
        Rice | RiceSale | Donation | Fidyah | Wealth $item,
    ) {
        $this->id = $id;
        $this->transaction_id = $transaction_id;
        $this->giver_name = $giver_name;
        $this->type = $type;
        $this->item = $item;
    }
}
