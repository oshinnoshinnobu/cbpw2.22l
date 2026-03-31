<div wire:init="initOne">

    <!-- Filter bar (always visible) -->
    <nav class="navbar navbar-expand-lg navbar-dark filterBar mb-4">
      <div class="container">
          <div class="btn-group btn-block text-center">
              <a wire:loading.remove wire:target="sortBy('view')" wire:click="sortBy('view')" class="btn blackBtn btn-sm userMenuBtn text-white" href="#"><i class="fas fa-redo"></i> Sort by Views</a>
              <a wire:loading wire:target="sortBy('view')" class="btn blackBtn btn-sm userMenuBtn text-white" href="#"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Sort by Views</a>
              <a wire:loading.remove wire:target="sortBy('random')" wire:click="sortBy('random')" class="btn blackBtn btn-sm userMenuBtn text-white" href="#"><i class="fas fa-dice"></i> Sort by Random</a>
              <a wire:loading wire:target="sortBy('random')" class="btn blackBtn btn-sm userMenuBtn text-white" href="#"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Sort by Random</a>
              <a wire:loading.remove wire:target="$emit('showModal')" wire:click="$emit('showModal')" type="button" class="btn blackBtn btn-sm text-white userMenuBtn"><i class="fas fa-chart-bar"></i> Stats</a>
              <a wire:loading wire:target="$emit('showModal')" type="button" class="btn blackBtn btn-sm text-white userMenuBtn"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Stats</a>
          </div>
      </div>
    </nav>

    @if ($invalidParams ?? false)
        <div class="container text-center">
            <div class="py-5">
                <div class="mb-4">
                    <i class="fas fa-search text-white" style="font-size: 4rem; opacity: 0.7;"></i>
                </div>
                <h4 class="text-white mb-3 font-weight-bold">No results</h4>
                <p class="text-white mb-4">
                    We couldn't find any matches for the provided search parameters. Please try adjusting your criteria.
                </p>
                <a href="{{ url('/') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    @elseif (empty($albums))
        <div class="text-center">
            <div class="page-load-status mt-4 mb-4">
                <div class="loader-ellips infinite-scroll-request">
                  <span class="loader-ellips__dot"></span>
                  <span class="loader-ellips__dot"></span>
                  <span class="loader-ellips__dot"></span>
                  <span class="loader-ellips__dot"></span>
                </div>
              </div>
        </div>
                        @else
                    <div class="container">
            <div class="row justify-content-center">
              <div class="col-md-12">
            <div class="row" id="albumsBox">
                @foreach ($albums as $album)
                <?php $imageLimitperAlbum = 0;?>

                    <div class="col-12 col-sm-4">
                <div class="card text-white indexCard mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                    @if ($album->type == config('myconfig.albumType.embedvideo'))
                    <small><strong><p class="cardAlbumTittle upperCaseTittles text-info" alt="{{$album->name}}">{{$album->name}}</p></strong></small>
                        @else
                        <small><strong><p class="cardAlbumTittle upperCaseTittles text-danger" alt="{{$album->name}}">{{$album->name}}</p></strong></small>
                    @endif
                    <small><p class="cardAlbumTittle lowerCaseTittles text-secondary">By: {{$album->user->name}}</p></small>
                    </div>
                    <div class="card-body cardIndexBodyPadding">
                        <p class="text-secondary dateIndexCard">{{$album->created_at;}}</p>
                        @if ( session('message') )
                        <div class="alert alert-success">{{ session('message') }}</div>
                      @endif
                      @if ($album->type == config('myconfig.albumType.media'))
                      <p class="cardAlbumDescription" alt="{{$album->description}}">{{$album->description}}</p>
                      @endif
                      @if ($album->type == config('myconfig.albumType.embedvideo'))
                        <div class="row">
                        @foreach ($embedvideos as $embedvideo)
                        @if ($embedvideo->album_id == $album->id)
                        <div class="col-md-3 col-3">
                            <img src="{{ config("myconfig.img.url") }}{{ $embedvideo->preview }}" class="imgThumbPublicIndex " data-was-processed='true'>
                        </div>
                        <div class="col-md-6 col-6">
                        <p class="cardAlbumDescription" alt="{{$album->description}}">{{$album->description}}</p>
                        </div>
                        @endif
                        @endforeach
                        </div>
                      @else
                      <div class="text-center">
                        @foreach ($images as $image)
                        @if (empty($image->album_id))
                            @else
                            @if($image->album_id == $album->id)
                            @if($imageLimitperAlbum != 4)
                            <?php $imageLimitperAlbum++; ?>
                            @if (($image->ext == "mp4" || $image->ext == "webm") && !$image->thumbnail_exist)
                            <img src="{{ config("myconfig.img.url") }}{{'/img/videothumb.png'}}" class="imgThumbPublicIndex " data-was-processed='true'>
                            @elseif ($image->ext == "mp4" || $image->ext == "webm")
                            <img src="{{ config("myconfig.img.url") }}{{ $image->url }}_thumb.jpg" class="imgThumbPublicIndex " data-was-processed='true'>
                            @else
                            <img src="{{ config("myconfig.img.url") }}{{ $image->url }}_thumb.{{$image->ext}}" class="imgThumbPublicIndex " data-was-processed='true'>
                            @endif
                            @endif
                            @endif
                        @endif
                        @endforeach
                    </div>
                      @endif
                        <div>
                            @foreach ($stats as $stat)
                            @if($stat->album->id == $album->id)
                            <span class="badge badge-Light"><i class="fas fa-images"></i><span class="badge badge-Light">{{$stat->qimage}}</span></span>
                            <span class="badge badge-Light"><i class="fas fa-film"></i><span class="badge badge-Light">{{$stat->qvideo}}</span></span>
                            <span class="badge badge-Light"><i class="fas fa-comments"></i><span class="badge badge-Light">{{$stat->qcomment}}</span></span>
                            <span class="badge badge-Light"><i class="fas fa-eye"></i><span class="badge badge-Light">{{$stat->view}}</span></span>
                            <span class="badge badge-Light"><i class="fas fa-heart"></i><span class="badge badge-Light">{{$stat->qlike}}</span></span>
                            <span class="badge badge-Light"><i class="fas fa-hdd"></i><span class="badge badge-Light"><?php echo app('App\Services\UtilsService')->formatSizeUnits($stat->size);?></span></span>
                            @endif
                            @endforeach
                        </div>

                    {{-- fin card body --}}
                    </div>
                    <div class="card-footer">
                        @foreach ($album->tags as $albumtags)
                        @if ($album->type == config('myconfig.albumType.embedvideo'))
                        <span class="badge badge-danger"><i class="fas fa-tag"></i><span alt="#{{$albumtags->name}}" class="badge badge-danger">{{$albumtags->name}}</span></span>
                        @else
                        <span class="badge badge-danger"><i class="fas fa-tag"></i><span alt="#{{$albumtags->name}}" class="badge badge-danger">{{$albumtags->name}}</span></span>
                        @endif
                        @endforeach
                        @if ($album->type == config('myconfig.albumType.embedvideo'))
                        <a href="{{route('album.content-e', $album->id)}}" class="stretched-link"></a>
                            @else
                            <a href="{{route('album.content', $album->id)}}" class="stretched-link"></a>
                        @endif
                    </div>
                </div>
            </div>
                @endforeach

        </div>

        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                @if ($albumMax == 0)

                @else
                <button wire:loading.remove wire:target='load' wire:click='load' class="btn loadBtn btn-sm text-white mb-2">
                    Load more
                </button>
                <button wire:loading wire:target='load' wire:loading class="btn loadBtn btn-sm mb-2">
                    <div class="page-load-status">
                        <div class="loader-ellips infinite-scroll-request">
                        <span class="loader-ellips__dot"></span>
                        <span class="loader-ellips__dot"></span>
                        <span class="loader-ellips__dot"></span>
                        <span class="loader-ellips__dot"></span>
                        </div>
                    </div>
                </button>
                @endif
            </div>
        </div>

      </div>
   </div>
</div>

@endif

</div>

