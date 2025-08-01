<?php

namespace App\Filters\Appointment;

use Closure;

class StatusFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        return $next($query);
    }
}
