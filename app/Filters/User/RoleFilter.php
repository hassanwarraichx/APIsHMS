<?php

namespace App\Filters\User;

use Closure;
class RoleFilter
{
    public function handle($request, Closure $next){
        if (request()->has('role')) {
            $request->whereHas('roles', function ($q) {
                $q->where('name', request('role'));
            });

        }

        return $next($request);
    }

}
