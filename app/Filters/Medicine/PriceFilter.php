<?php

namespace App\Filters\Medicine;

use Closure;

class PriceFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('price_min')) {
            $query->where('price', '>=', request('price_min'));
        }

        if (request()->filled('price_max')) {
            $query->where('price', '<=', request('price_max'));
        }

        return $next($query);
    }
}
