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
<script src="{{ elixir('js/all.js') }}"></script>

{{-- including CDN assets --}}
@include('partials.cdn')

<script>
    $('div.alert').delay(3000).slideUp(300);
</script>