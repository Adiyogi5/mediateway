<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureDrpSession
{
    public function handle($request, Closure $next)
    {
        if (auth('drp')->check()) {
            if (!$request->session()->has('slug')) {
                $firstDrp = auth('drp')->user();
                $request->session()->put('drpId', $firstDrp->id);
                $request->session()->put('slug', $firstDrp->slug);
            }
        }

        return $next($request);
    }
}
