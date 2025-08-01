<?php

namespace App\Filters\User;

use Closure;

class NameFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('name')) {
            $name = request('name');
            $query->where('name', 'like', "%$name%");
        }

        return $next($query);
    }
}

