<?php

namespace App\Http\Middleware;

use Closure;

class SanitizeInput
{
    /**
     * Handle an incoming request and sanitize GET parameters to prevent XSS and injection attacks
     */
    public function handle($request, Closure $next)
    {
        // Only process GET requests
        if ($request->isMethod('get')) {
            $sanitized = [];

            foreach ($request->all() as $key => $value) {
                // REJECT if value is an array (array injection attempt)
                if (is_array($value)) {
                    // Log attempted array injection
                    \Log::warning('Array injection attempt detected', [
                        'key' => $key,
                        'value' => json_encode($value),
                        'ip' => $request->ip(),
                        'url' => $request->url()
                    ]);
                    // Set to empty string - completely block the array
                    $sanitized[$key] = '';
                    continue;
                }

                // Force to string and sanitize
                if (is_string($value)) {
                    $sanitized[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
                } else {
                    $sanitized[$key] = $value;
                }
            }

            // Replace all request input with sanitized values
            $request->replace($sanitized);
        }

        return $next($request);
    }
}
