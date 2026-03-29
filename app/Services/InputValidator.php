<?php

namespace App\Services;

class InputValidator
{
    /**
     * Validate sortBy parameter for LoadMoreAlbum
     */
    public static function validateSortBy($sortBy)
    {
        $validSorts = ['', 'random', 'view'];
        return in_array($sortBy, $validSorts, true);
    }
}
