<?php

namespace App\Filters\Billing;

use Closure;

class PendingBillingFilter
{
    public function handle($query, Closure $next)
    {
        $query->whereHas('prescription')
            ->whereDoesntHave('bill');

        return $next($query);
    }
}
