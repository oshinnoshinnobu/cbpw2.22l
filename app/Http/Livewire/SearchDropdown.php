<?php

namespace App\Http\Livewire;

use App\Models\Album;
use App\Models\Image;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\UtilsService;
use App\Services\InputSanitizer;

class SearchDropdown extends Component
{
    public $search = "";
    public $isRateLimited = false;
    protected $listeners = ['refreshSearchBar'];

    // Add validation rules
    protected $rules = [
        'search' => 'string|max:100',
    ];


    public function mount()
    {
        $this->search = "";
        $this->search = InputSanitizer::sanitize($this->search, 'SearchDropdown::mount');

        $this->checkRateLimit();
    }

    protected function checkRateLimit()
    {
        $throttleKey = 'search_' . $this->getClientIp();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $this->isRateLimited = true;
        }
    }

    public function refreshSearchBar()
    {
        $this->search = "";
        $this->search = InputSanitizer::sanitize($this->search, 'SearchDropdown::refreshSearchBar');
    }

    public function updatedSearch()
    {
        $this->validateOnly('search');

        // sanitize before validation to prevent array injection/XSS
        $this->search = InputSanitizer::sanitize($this->search, 'SearchDropdown::updatedSearch');

        $throttleKey = 'search_' . $this->getClientIp();
        RateLimiter::hit($throttleKey, 900); // 15 minutes limit

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $this->addError('search', 'Too many searches. Search is temporarily disabled.');
            $this->isRateLimited = true;
        }
    }

    private function getClientIp()
    {
        // Use UtilsService if available, otherwise fallback
        if (class_exists('App\Services\UtilsService')) {
            return UtilsService::getClientIp();
        }
        return request()->ip();
    }

    public function render()
    {
        $albums = collect();
        $images = collect();

        if (strlen($this->search) > 2 && !$this->isRateLimited) {

            // Escape LIKE wildcards after sanitization
            $searchTerm = addcslashes($this->search, '%_');

            if (Auth::check() && auth()->user()->type == config('myconfig.privileges.super')) {
                $albums = Album::where('name', 'like', '%' . $searchTerm . '%')
                    ->orderBy('updated_at', 'desc')
                    ->limit(7)
                    ->get();
            } else {
                $albums = Album::where('visibility', 1)
                    ->where('name', 'like', '%' . $searchTerm . '%')
                    ->orderBy('updated_at', 'desc')
                    ->limit(7)
                    ->get();
            }

            // Get images for albums
            foreach ($albums as $album) {
                $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->first());
            }
        }

        return view('livewire.search-dropdown', [
            'albums' => $albums,
            'images' => $images
        ]);
    }
}
