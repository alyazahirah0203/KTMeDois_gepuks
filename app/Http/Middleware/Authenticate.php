<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Determine if the user is logged in to any of the guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function authenticate($request, array $guards)
    {
        // Check if user is authenticated via vendor guard OR web guard
        if (Auth::guard('vendor')->check()) {
            return;
        }

        if (Auth::check()) {
            return;
        }

        $this->unauthenticated($request, $guards);
    }
}