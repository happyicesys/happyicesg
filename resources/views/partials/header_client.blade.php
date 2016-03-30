{{-- app title --}}
<title>{{ $APP_NAME }} | @yield('title')</title>
{{-- content setting --}}
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
{{-- theme style --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
{{-- seo keywords--}}
<meta name="keywords" content="Happy Ice, Ice Cream, Healthier Choice, Healthier Life, Xiao Mei Ice Cream, Made In Taiwan, Flavoured Ice Cream, Chocolate, Vanilla, Red Bean Taro, QQ Pudding, Choc Pie, Japanese Matcha, Low Fat Frozen Yogurt, Oshare"/>


{{-- CSS & Javascript versioning gulpfile --}}
<link rel="stylesheet" href="{{ elixir('css/all.css') }}">
<link rel="stylesheet" href="/css/creative_theme.css">

<script src="{{ elixir('js/all.js') }}"></script>
<script src="/js/creative_theme.js"></script>

{{-- including CDN assets --}}
@include('partials.cdn')

{{-- Custom Fonts --}}
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
