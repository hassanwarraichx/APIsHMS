<?php

namespace App\Filters\Appointment;

use Closure;

class PatientFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('patient_id')) {
            $query->where('patient_id', request('patient_id'));
        }

        return $next($query);
    }
}

