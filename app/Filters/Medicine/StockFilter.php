<?php

namespace App\Filters\Medicine;

use Closure;

class StockFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('stock_min')) {
            $query->where('stock', '>=', request('stock_min'));
        }

        if (request()->filled('stock_max')) {
            $query->where('stock', '<=', request('stock_max'));
        }

        return $next($query);


    }

}
