<?php

namespace App\Contracts;

use App\Models\RiceItem;
use Illuminate\Database\Eloquent\Collection;

interface RiceItemServiceInterface
{
    public function getAllRiceItems(): Collection;
    public function createRiceItem(array $data): RiceItem;
}
