<?php

namespace App\Contracts;

use App\Models\DefaultValue;

interface DefaultValueServiceRepository
{
    public function firstOrCreate(): DefaultValue;
}
