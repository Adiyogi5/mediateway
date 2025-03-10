<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureIndividualSession
{
    public function handle($request, Closure $next)
    {
        if (auth('individual')->check()) {
            if (!$request->session()->has('slug')) {
                $firstIndividual = auth('individual')->user();
                $request->session()->put('indivudualId', $firstIndividual->id);
                $request->session()->put('slug', $firstIndividual->slug);
            }
        }

        return $next($request);
    }
}
