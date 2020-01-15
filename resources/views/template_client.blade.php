<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header_client')
        @yield('header')
    </head>

    <body id="page-top">
        @include('partials.nav_client')
            <div style="padding-top: 50px">
                @include('errors.validate')
                @include('flash::message')
            </div>
                @yield('content')
            @include('partials.footer_client')
        @yield('footer')
        <script>
            $('div.alert').not('.alert-important').delay(6000).fadeOut(350);
        </script>
        <script src="https://www.w3counter.com/tracker.js?id=129734"></script>
    </body>
</html>

