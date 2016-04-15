<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header_client')
    </head>

    <body id="page-top">
        @include('partials.nav_client')
            @include('errors.validate')
            @include('flash::message')
                @yield('content')
            @include('partials.footer_client')
        @yield('footer')
        <script>
            $('div.alert').delay(3000).slideUp(300);
        </script>
    </body>
</html>

