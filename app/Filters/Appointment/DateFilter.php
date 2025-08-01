<?php

namespace App\Filters\Appointment;

use Closure;

class DateFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('date_from')) {
            $query->whereDate('appointment_time', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->whereDate('appointment_time', '<=', request('date_to'));
        }

        return $next($query);
    }
}
