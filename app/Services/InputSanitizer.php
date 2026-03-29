<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class InputSanitizer
{
    /**
     * Sanitize input to prevent XSS and array injection attacks
     */
    public static function sanitize($input, $source = 'Unknown')
    {
        // REJECT arrays completely
        if (is_array($input)) {
            Log::warning('Array injection attempt blocked', [
                'source' => $source,
                'input' => json_encode($input),
                'ip' => request()->ip(),
                'url' => request()->url()
            ]);
            return '';
        }

        if (empty($input)) {
            return '';
        }

        // Force to string (prevents array injection)
        $input = (string)$input;

        // Strip HTML tags and encode special characters
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}
