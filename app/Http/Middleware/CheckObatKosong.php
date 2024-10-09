<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckObatKosong
{
    public function handle(Request $request, Closure $next)
    {
        $obatKosong = Cache::get('obat_kosong', []);
        view()->share('obatKosong', $obatKosong);
        return $next($request);
    }
}