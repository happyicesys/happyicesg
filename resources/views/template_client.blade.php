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
        @yield('footer')

    </body>
</html>

