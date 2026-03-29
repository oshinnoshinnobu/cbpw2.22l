<?php

namespace App\Http\Middleware;

use Closure;

class SecureHeaders
{
    /**
     * Handle an incoming request and add security headers
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Prevents browsers from interpreting content as something else
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevents clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');

        // Enables XSS protection in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controls referrer information sent to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy
        $response->headers->set('Content-Security-Policy', $this->getCSP());

        // Permissions Policy (formerly Feature-Policy)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Strict Transport Security (if HTTPS is enabled)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    /**
     * Get Content Security Policy header value
     * Adjust based on your needs (external CDNs, scripts, etc.)
     */
    private function getCSP()
    {
        // For development/local, allow external resources (fonts, etc.)
        if (!app()->environment('production')) {
            // Development: allow all layout libraries/CDNs used in app.blade.php
            return "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'; "
                . "script-src 'self' 'unsafe-inline' 'unsafe-eval' http: https: https://www.googletagmanager.com https://js.juicyads.com https://poweredby.jads.co https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://use.fontawesome.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                . "font-src 'self' data: https://fonts.gstatic.com https://use.fontawesome.com https://cdn.jsdelivr.net; "
                . "img-src 'self' data: https:; connect-src 'self' https://www.google-analytics.com https://cdnjs.cloudflare.com; frame-src 'self' https://adserver.juicyads.com http://adserver.juicyads.com; frame-ancestors 'none'; form-action 'self';";
        }

        // Production: allow JuicyAds frames, Google Analytics, and required layout libraries/CDNs
        return "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' http: https: https://www.googletagmanager.com https://js.juicyads.com https://poweredby.jads.co https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://use.fontawesome.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "font-src 'self' data: https://fonts.gstatic.com https://use.fontawesome.com https://cdn.jsdelivr.net; "
            . "img-src 'self' data: https:; connect-src 'self' https://www.google-analytics.com https://cdnjs.cloudflare.com; frame-src 'self' https://adserver.juicyads.com http://adserver.juicyads.com; frame-ancestors 'none'; form-action 'self';";
    }
}
