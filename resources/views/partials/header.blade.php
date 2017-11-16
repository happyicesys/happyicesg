<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Happyice') }} | @yield('title')</title>

{{-- including CDN assets --}}
@include('partials.cdn')

{{-- CSS & Javascript versioning gulpfile --}}
<link rel="stylesheet" href="{{ elixir('css/all.css') }}">
<script src="{{ elixir('js/all.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSegGsLqW4GNNPPg2NbjlJ1uXMCoN4s1c" async defer></script>

