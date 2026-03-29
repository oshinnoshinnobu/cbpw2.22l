<?php

namespace App\Http\Livewire;

use App\Models\Album;
use App\Models\EmbedVideo;
use App\Models\Image;
use App\Models\Stat;
use App\Services\InputSanitizer;
use App\Services\InputValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Livewire\Component;

class LoadMoreAlbum extends Component
{

    public $amount = 6;
    public $readyToLoad = false;
    public $sortBy = "";
    public $invalidParams = false;

    protected $queryString = [
        'sortBy' => ['except' => ''],
    ];

    public function mount()
    {
        // Sanitize and validate sortBy
        $this->sortBy = is_string($this->sortBy) ? $this->sortBy : '';

        $this->sortBy = InputSanitizer::sanitize($this->sortBy, 'LoadMoreAlbum::mount');

        if (!InputValidator::validateSortBy($this->sortBy)) {
            $this->invalidParams = true;
        }
    }

    public function render()
    {
        // Sanitize and validate sortBy on every render
        $this->sortBy = is_string($this->sortBy) ? $this->sortBy : '';

        $this->sortBy = InputSanitizer::sanitize($this->sortBy, 'LoadMoreAlbum::render');

        if (!InputValidator::validateSortBy($this->sortBy)) {
            $this->invalidParams = true;
        }

        // If invalid params, render view with empty data but do not return early
        if ($this->invalidParams) {
            return view('livewire.load-more-album', [
                'albums' => collect(),
                'images' => collect(),
                'stats' => collect(),
                'albumMax' => 0,
                'embedvideos' => collect(),
                'invalidParams' => $this->invalidParams,
            ]);
        }

        if ($this->readyToLoad) {
            // ...existing code...
            if ($this->sortBy == 'random') {
                if (Auth::check() && auth()->user()->type == config('myconfig.privileges.super')) {
                    $albums = Album::take($this->amount)->inRandomOrder()->get();
                } else {
                    $albums = Album::take($this->amount)->where('visibility', 1)->inRandomOrder()->get();
                }
                $stats = Stat::whereIn('album_id', $albums->pluck('id'))->get();
                $embedvideos = EmbedVideo::whereIn('album_id', $albums->pluck('id'))->get();
                $images = collect();
                foreach ($albums as $album) {
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(1)->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(2)->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(3)->first());
                }
                return view('livewire.load-more-album', [
                    'albums' => $albums,
                    'images' => $images,
                    'stats' => $stats,
                    'albumMax' => $this->albumMax($albums),
                    'embedvideos' => $embedvideos,
                ]);
            } else if ($this->sortBy == 'view') {
                if (Auth::check() && auth()->user()->type == config('myconfig.privileges.super')) {
                    $stats = Stat::orderBy('view', 'desc')->get();
                } else {
                    $stats = Stat::whereIn('album_id', Album::where('visibility', 1)->get()->pluck('id'))->orderBy('view', 'desc')->get();
                }
                if ($stats->isNotEmpty()) {
                    $albums = Album::take($this->amount)->whereIn('id', $stats->pluck('album_id'))->orderByRaw('FIELD(id,' . implode(',', $stats->pluck('album_id')->toArray()) . ')')->get();
                } else {
                    if (Auth::check() && auth()->user()->type == config('myconfig.privileges.super')) {
                        $albums = Album::take($this->amount)->orderBy('updated_at', 'desc')->get();
                    } else {
                        $albums = Album::take($this->amount)->where('visibility', 1)->orderBy('updated_at', 'desc')->get();
                    }
                }
                $embedvideos = EmbedVideo::whereIn('album_id', $albums->pluck('id'))->get();
                $images = collect();
                foreach ($albums as $album) {
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(1)->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(2)->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(3)->first());
                }
                return view('livewire.load-more-album', [
                    'albums' => $albums,
                    'images' => $images,
                    'stats' =>  $stats,
                    'albumMax' => $this->albumMax($albums),
                    'embedvideos' => $embedvideos,
                ]);
            } else {
                if (Auth::check() && auth()->user()->type == config('myconfig.privileges.super')) {
                    $albums = Album::take($this->amount)->orderBy('updated_at', 'desc')->get();
                } else {
                    $albums = Album::take($this->amount)->where('visibility', 1)->orderBy('updated_at', 'desc')->get();
                }
                $stats = Stat::whereIn('album_id', $albums->pluck('id'))->get();
                $embedvideos = EmbedVideo::whereIn('album_id', $albums->pluck('id'))->get();
                $images = collect();
                foreach ($albums as $album) {
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(1)->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(2)->first());
                    $images->add(Image::where('album_id', $album->id)->orderBy('id', 'desc')->skip(3)->first());
                }
                return view('livewire.load-more-album', [
                    'albums' => $albums,
                    'images' => $images,
                    'stats' => $stats,
                    'albumMax' => $this->albumMax($albums),
                    'embedvideos' => $embedvideos,
                ]);
            }
        } else {
            return view('livewire.load-more-album');
        }
    }

    public function sortBy($name)
    {
        $name = is_string($name) ? $name : '';
        $name = InputSanitizer::sanitize($name, 'LoadMoreAlbum::sortBy');
        if (!InputValidator::validateSortBy($name)) {
            $this->invalidParams = true;
            return;
        }
        // valid sort selected -> clear invalid flag so UI becomes interactive
        $this->invalidParams = false;
        $this->sortBy = $name;
    }

    public function load()
    {
        $this->amount += 6;
    }

    public function initOne()
    {
        $this->readyToLoad = true;
    }

    public function albumMax($albums)
    {
        if (count($albums) >= $this->amount) {
            return 1;
        } else {
            return 0;
        }
    }
}
