<?php

namespace App\DTO;

use Illuminate\Support\Carbon;

class TransactionDTO {
    public string $transaction_number;
    public Carbon $date;
    public string $customer;
    public string $address;
    public string $wa_number;
    public string $officer_name;
    public int $created_by;
    public Carbon $created_at;
    public Carbon $updated_at;
    public array $transaction_details;

    public function __construct(
        string $transaction_number,
        Carbon $date,
        string $customer,
        string $address,
        string $wa_number,
        string $officer_name,
        int $created_by,
        array $transaction_details = []

    ) {
        $this->transaction_number = $transaction_number;
        $this->date = $date;
        $this->customer = $customer;
        $this->address = $address;
        $this->wa_number = $wa_number;
        $this->officer_name = $officer_name;
        $this->created_by = $created_by;
        $this->transaction_details = $transaction_details;
    }
}
