<?php

use App\Services\DefaultValueService;

if (! function_exists('defaultValue')) {
    function defaultValue()
    {
        $service = app(DefaultValueService::class);
        return $service->firstOrCreate();
    }
}
