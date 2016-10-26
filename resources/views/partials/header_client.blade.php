{{-- app title --}}
<title>{{ $APP_NAME }} | @yield('title')</title>
{{-- content setting --}}
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
{{-- theme style --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
{{-- seo keywords--}}
<meta name="keywords" content="Happy Ice, Ice Cream, Healthier Choice, Healthier Life, Xiao Mei Ice Cream, Made In Taiwan, Flavoured Ice Cream, Chocolate, Vanilla, Red Bean Taro, QQ Pudding, Choc Pie, Japanese Matcha, Low Fat Frozen Yogurt, Oshare"/>
<meta id="_token" name="_token" content="{{ csrf_token() }}">

{{-- CSS & Javascript versioning gulpfile --}}
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css">

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/font-awesome/css/font-awesome.min.css" type="text/css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="/css/animate.min.css" type="text/css">

    <!-- Select2 -->
    <link rel="stylesheet" href="/css/creative.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/select2.min.css" type="text/css">

    <!-- videojs cdn -->
    <link href="//vjs.zencdn.net/5.4.6/video-js.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    {{-- including CDN assets --}}
    @include('partials.cdn')
    <script src="{{ elixir('js/all.js') }}"></script>
    <!-- jQuery -->
    <script src="/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/js/bootstrap.min.js"></script>

    <script src="/js/dirPagination.js"></script>

    <!-- Plugin JavaScript -->
    <script src="/js/jquery.easing.min.js"></script>
    <script src="/js/jquery.fittext.js"></script>
    <script src="/js/wow.min.js"></script>

    <!-- select2 -->
    <script src="/js/select2.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/js/creative.js"></script>

    <!-- videojs cdn -->
    <script src="//vjs.zencdn.net/5.4.6/video.min.js"></script>
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>

        Vue.http.interceptors.push((request, next) => {
            request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken);

            next();
        });
    </script>

<style type="text/css">
    body {
        font-family: 'Lato';
    }
</style>

