<?php

namespace App\Contracts;

use App\Models\RiceItem;
use Illuminate\Database\Eloquent\Collection;

interface RiceItemRepositoryInterface
{
    public function getAll(array $relations = []): Collection;
    public function create(array $data): RiceItem;
}
