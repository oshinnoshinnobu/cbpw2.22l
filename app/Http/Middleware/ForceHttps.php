<?php

namespace App\Http\Middleware;

use Closure;

class ForceHttps
{
    /**
     * Force HTTPS in production environment
     */
    public function handle($request, Closure $next)
    {
        // Force HTTPS in production
        if (app()->environment('production') && !$request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
