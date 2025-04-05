<?php

namespace Kho8k\ThemeVung\Controllers;

use Backpack\Settings\app\Models\Setting;
use Illuminate\Http\Request;
use Kho8k\Core\Models\Actor;
use Kho8k\Core\Models\Catalog;
use Kho8k\Core\Models\Category;
use Kho8k\Core\Models\Director;
use Kho8k\Core\Models\Episode;
use Kho8k\Core\Models\Movie;
use Kho8k\Core\Models\Region;
use Kho8k\Core\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ThemeVungController
{
    public function index()
    {
        return view('themes::themevung.index', [
            'title' => Setting::get('site_homepage_title')
        ]);
    }

    public function getMovieOverview(Request $request)
    {
        /** @var Movie */
        $movie = Movie::fromCache()->find($request->movie ?: $request->id);

        if (is_null($movie)) abort(404);

        $movie->generateSeoTags();

        $movie->increment('view_total', 1);
        $movie->increment('view_day', 1);
        $movie->increment('view_week', 1);
        $movie->increment('view_month', 1);

        $movie_related_cache_key = 'movie_related:' . $movie->id;
        $movie_related = Cache::get($movie_related_cache_key);
        if (is_null($movie_related)) {
            $movie_related = $movie->categories[0]->movies()->inRandomOrder()->limit(get_theme_option('movie_related_limit', 10))->get();
            Cache::put($movie_related_cache_key, $movie_related, setting('site_cache_ttl', 5 * 60));
        }

        return view('themes::themevung.single', [
            'currentMovie' => $movie,
            'title' => $movie->getTitle(),
            'movie_related' => $movie_related
        ]);
    }

    public function getEpisode(Request $request)
    {
        $movie = Movie::fromCache()->find($request->movie ?: $request->movie_id)->load('episodes');

        if (is_null($movie)) abort(404);

        /** @var Episode */
        $episode_id = $request->id;
        $episode = $movie->episodes->when($episode_id, function ($collection, $episode_id) {
            return $collection->where('id', $episode_id);
        })->firstWhere('slug', $request->episode);

        if (is_null($episode)) abort(404);

        $episode->generateSeoTags();

        $movie->increment('view_total', 1);
        $movie->increment('view_day', 1);
        $movie->increment('view_week', 1);
        $movie->increment('view_month', 1);

        $movie_related_cache_key = 'movie_related:' . $movie->id;
        $movie_related = Cache::get($movie_related_cache_key);
        if (is_null($movie_related)) {
            $movie_related = $movie->categories[0]->movies()->inRandomOrder()->limit(get_theme_option('movie_related_limit', 10))->get();
            Cache::put($movie_related_cache_key, $movie_related, setting('site_cache_ttl', 5 * 60));
        }

        return view('themes::themevung.episode', [
            'currentMovie' => $movie,
            'movie_related' => $movie_related,
            'episode' => $episode,
            'title' => $episode->getTitle()
        ]);
    }

    public function getMovieOfCategory(Request $request)
    {
        /** @var Category */
        $category = Category::fromCache()->find($request->category ?: $request->id);

        if (is_null($category)) abort(404);

        $category->generateSeoTags();

        $movies = $category->movies()->orderBy('created_at', 'desc')->paginate(get_theme_option('per_page_limit'));

        return view('themes::themevung.catalog', [
            'data' => $movies,
            'category' => $category,
            'title' => $category->seo_title ?: $category->getTitle(),
            'section_name' => "Phim thể loại $category->name"
        ]);
    }

    public function getMovieOfRegion(Request $request)
    {
        /** @var Region */
        $region = Region::fromCache()->find($request->region ?: $request->id);

        if (is_null($region)) abort(404);

        $region->generateSeoTags();

        $movies = $region->movies()->orderBy('created_at', 'desc')->paginate(get_theme_option('per_page_limit'));

        return view('themes::themevung.catalog', [
            'data' => $movies,
            'region' => $region,
            'title' => $region->seo_title ?: $region->getTitle(),
            'section_name' => "Phim quốc gia $region->name"
        ]);
    }

    public function getMovieOfActor(Request $request)
    {
        /** @var Actor */
        $actor = Actor::fromCache()->find($request->actor ?: $request->id);

        if (is_null($actor)) abort(404);

        $actor->generateSeoTags();

        $movies = $actor->movies()->orderBy('created_at', 'desc')->paginate(get_theme_option('per_page_limit'));

        return view('themes::themevung.catalog', [
            'data' => $movies,
            'person' => $actor,
            'title' => $actor->getTitle(),
            'section_name' => "Diễn viên $actor->name"
        ]);
    }

    public function getMovieOfDirector(Request $request)
    {
        /** @var Director */
        $director = Director::fromCache()->find($request->director ?: $request->id);

        if (is_null($director)) abort(404);

        $director->generateSeoTags();

        $movies = $director->movies()->orderBy('created_at', 'desc')->paginate(get_theme_option('per_page_limit'));

        return view('themes::themevung.catalog', [
            'data' => $movies,
            'person' => $director,
            'title' => $director->getTitle(),
            'section_name' => "Đạo diễn $director->name"
        ]);
    }

    public function getMovieOfTag(Request $request)
    {
        /** @var Tag */
        $tag = Tag::fromCache()->find($request->tag ?: $request->id);

        if (is_null($tag)) abort(404);

        $tag->generateSeoTags();

        $movies = $tag->movies()->orderBy('created_at', 'desc')->paginate(get_theme_option('per_page_limit'));
        return view('themes::themevung.catalog', [
            'data' => $movies,
            'tag' => $tag,
            'title' => $tag->getTitle(),
            'section_name' => "Tags: $tag->name"
        ]);
    }

    public function getMovieOfType(Request $request)
    {
        /** @var Catalog */
        $catalog = Catalog::fromCache()->find($request->type ?: $request->id);

        if (is_null($catalog)) abort(404);

        $catalog->generateSeoTags();

        $cache_key = 'catalog:' . $catalog->id . ':page:' . ($request['page'] ?: 1);
        $movies = Cache::get($cache_key);
        if (is_null($movies)) {
            $value = explode('|', trim($catalog->value));
            [$relation_config, $field, $val, $sortKey, $alg] = array_merge($value, ['', 'is_copyright', 0, 'created_at', 'desc']);
            $relation_config = explode(',', $relation_config);

            [$relation_table, $relation_field, $relation_val] = array_merge($relation_config, ['', '', '']);
            try {
                $movies = \Kho8k\Core\Models\Movie::when($relation_table, function ($query) use ($relation_table, $relation_field, $relation_val, $field, $val) {
                    $query->whereHas($relation_table, function ($rel) use ($relation_field, $relation_val, $field, $val) {
                        $rel->where($relation_field, $relation_val)->where(array_combine(explode(",", $field), explode(",", $val)));
                    });
                })->when(!$relation_table, function ($query) use ($field, $val) {
                    $query->where(array_combine(explode(",", $field), explode(",", $val)));
                })
                    ->orderBy($sortKey, $alg)
                    ->paginate($catalog->paginate);

                Cache::put($cache_key, $movies, setting('site_cache_ttl', 5 * 60));
            } catch (\Exception $e) {
            }
        }

        return view('themes::themevung.catalog', [
            'data' => $movies,
            'section_name' => "Danh sách $catalog->name"
        ]);
    }

    public function reportEpisode(Request $request, $movie, $slug)
    {
        $movie = Movie::fromCache()->find($movie)->load('episodes');

        $episode = $movie->episodes->when(request('id'), function ($collection) {
            return $collection->where('id', request('id'));
        })->firstWhere('slug', $slug);

        $episode->update([
            'report_message' => request('message', ''),
            'has_report' => true
        ]);

        return response([], 204);
    }

    public function rateMovie(Request $request, $movie)
    {

        $movie = Movie::fromCache()->find($movie);

        $movie->refresh()->increment('rating_count', 1, [
            'rating_star' => $movie->rating_star +  ((int) request('rating') - $movie->rating_star) / ($movie->rating_count + 1)
        ]);

        return response()->json(['status' => true, 'rating_star' => number_format($movie->rating_star, 1), 'rating_count' => $movie->rating_count]);
    }

    public function filter(Request $request)
    {
        if (!$request->has('search') && !$request->has('filter')) {
            return response()->json([
                'status' => false,
                'message' => 'Không có dữ liệu để lọc.',
            ]);
        }

        $data = Movie::when(!empty($request['filter']['category']), function ($movie) use ($request) {
            $movie->whereHas('categories', function ($categories) use ($request) {
                $categories->where('id', $request['filter']['category']);
            });
        })->when(!empty($request['filter']['region']), function ($movie) use ($request) {
            $movie->whereHas('regions', function ($regions) use ($request) {
                $regions->where('id', $request['filter']['region']);
            });
        })->when(!empty($request['filter']['year']), function ($movie) use ($request) {
            $movie->where('publish_year', $request['filter']['year']);
        })->when(!empty($request['filter']['type']), function ($movie) use ($request) {
            $movie->where('type', $request['filter']['type']);
        })->when(!empty($request['search']), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request['search'] . '%')
                    ->orWhere('origin_name', 'like', '%' . $request['search'] . '%');
            });
        })->when(!empty($request['filter']['sort']), function ($movie) use ($request) {
            switch ($request['filter']['sort']) {
                case 'create':
                    return $movie->orderBy('created_at', 'desc');
                case 'update':
                    return $movie->orderBy('updated_at', 'desc');
                case 'year':
                    return $movie->orderBy('publish_year', 'desc');
                case 'view':
                    return $movie->orderBy('view_total', 'desc');
            }
        })->paginate(get_theme_option('per_page_limit'));

        if ($data->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Không tìm thấy phim phù hợp.',
                'html' => view('themes::themevung.inc.film_filter', [
                    'data' => $data,
                    'search' => $request['search'],
                    'section_name' => "Tìm kiếm phim: $request->search"
                ])->render()
            ]);
        }

        return response()->json([
            'status' => true,
            'data'  => $data,
            'message' => 'Lọc phim thành công',
            'html' => view('themes::themevung.inc.film_filter', [
                'data' => $data,
                'search' => $request['search'],
                'section_name' => "Tìm kiếm phim: $request->search"
            ])->render()
        ]);
    }
}
