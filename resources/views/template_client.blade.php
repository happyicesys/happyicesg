<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header_client')
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
    </body>
</html>

