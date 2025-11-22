<?php

namespace App\Repositories;

use App\Contracts\RiceItemRepositoryInterface;
use App\Models\RiceItem;
use Illuminate\Database\Eloquent\Collection;

class RiceItemRepository implements RiceItemRepositoryInterface
{
    public function getAll(array $relations = []): Collection
    {
        return RiceItem::with($relations)->get();
    }

    public function create(array $data): RiceItem
    {
        return RiceItem::create($data);
    }
}
