<?php

namespace App\Services;

use App\Contracts\DefaultValueServiceRepository;
use App\Models\DefaultValue;

class DefaultValueService implements DefaultValueServiceRepository
{
    public function firstOrCreate(): DefaultValue
    {
        return DefaultValue::firstOrCreate();
    }
}