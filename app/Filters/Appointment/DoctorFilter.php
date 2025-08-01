<?php

namespace App\Filters\Appointment;

use Closure;

class DoctorFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('doctor_id')) {
            $query->where('doctor_id', request('doctor_id'));
        }

        return $next($query);
    }
}

