<?php

namespace App\Filters\Medicine;

use Closure;

class BrandFilter
{
    public function handle($query,Closure $next )
    {
        if(request()->filled('brand')){
            $query->where('brand', 'like', "%".request('brand')."%");
        }
        return $next( $query );
    }

}
