<?php

namespace App\Filters\Medicine;

use Closure;
class ExpiryDateFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('expiry_before')) {
            $query->whereDate('expiry_date', '<=', request('expiry_before'));
        }

        if (request()->filled('expiry_after')) {
            $query->whereDate('expiry_date', '>=', request('expiry_after'));
        }

        return $next($query);
    }

}
