<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header_client')
    </head>

    <body id="page-top">
        @include('partials.nav_client')
            <div style="margin: 50px 0px 0px 0px;">
                @include('errors.validate')
                @include('flash::message')
            </div>
                @yield('content')
            @include('partials.footer_client')
        @yield('footer')
    </body>
</html>

