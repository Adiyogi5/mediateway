<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureOrganizationSession
{
    public function handle($request, Closure $next)
    {
        if (auth('organization')->check()) {
            if (!$request->session()->has('slug')) {
                $firstOrganization = auth('organization')->user();
                $request->session()->put('organizationId', $firstOrganization->id);
                $request->session()->put('slug', $firstOrganization->slug);
            }
        }
       
        return $next($request);
    }
}
