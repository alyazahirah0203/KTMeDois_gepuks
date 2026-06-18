<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ITOfficer
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isITOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. IT Officer only.');
        }

        return $next($request);
    }
}