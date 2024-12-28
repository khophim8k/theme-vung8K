<a href="{{ $movie->getUrl() }}" class="col-xs-4 col-lg-2 film-small" id="film-item">
    <div class="poster-film-small">
        <img src="{{ $movie->getThumbUrl() }}" alt="thumb">
        @if ($movie->type == 'series')
            <div class="sotap">{{ $movie->episode_current }}</div>
        @endif
        <div class="play"></div>
    </div>
    <div class="categories-wrapper">
        @foreach ($movie->categories->take(3) as $category)
            <div class="category-item">{{ $category->name }}</div>
        @endforeach
    </div>
    <div class="title-film-small">
        <b class="title-film">{{ $movie->name }}</b>
        <p>{{ $movie->origin_name }} ({{ $movie->publish_year }})</p>
    </div>
</a>
