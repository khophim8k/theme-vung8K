<div class="group-film">
    <h2>
        <a href="">phim đề cử <i class="fa fa-caret-right" aria-hidden="true"></i>
        </a>
    </h2>
    <span class="line-ngang"></span>
    <div class="phimdecu-slider">
        @foreach ($recommendations as $movie)
        <div class="item" id="recommanditem">
            <a href="{{$movie->getUrl()}}">
                <img src="{{$movie->getThumbUrl()}}" alt="thumb"> 
                <div class="sotap">{{$movie->episode_current}}</div>
                <div class="black-gradient">
                    <b class="title-film">{{$movie->name}}</b>
                    <p>{{$movie->origin_name}} ({{$movie->publish_year}})</p>
                    {{-- <ul class="tag-film">
                        <li>
                            <div class="hd">{{$movie->quality}}</div>
                        </li>
                        @if (strpos(strtolower($movie->language), 'thuyết minh'))
                        <li>
                            <div class="sd tm">TM</div>
                        </li>
                        @endif
                    </ul> --}}
                </div>
                <div class="play"></div>
            </a>
        </div>
        @endforeach
    </div>
</div>
