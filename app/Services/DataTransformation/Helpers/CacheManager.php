<?php

namespace App\Services\DataTransformation\Helpers;

use Cache;

class CacheManager
{
    public function remember(int $claimId, int $endpointId, callable $callback)
    {
        $cacheKey = "transformed_data_{$claimId}_{$endpointId}";
        return Cache::remember($cacheKey, now()->addMinutes(5), $callback);
    }
}
