@extends('themes::themevung.layout')

@php
    $years = Cache::remember(
        'all_years',
        \Backpack\Settings\app\Models\Setting::get('site_cache_ttl', 5 * 60),
        function () {
            return \Kho8k\Core\Models\Movie::select('publish_year')->distinct()->pluck('publish_year')->sortDesc();
        },
    );
@endphp

@section('content')
    @include('themes::themevung.inc.film_filter')
@endsection

@push('scripts')
    <script defer type="text/javascript" src="/themes/vung/js/customize.js"></script>
@endpush
