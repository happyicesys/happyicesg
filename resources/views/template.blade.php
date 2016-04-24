<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header')
    </head>

    <body>
        @include('partials.nav')
        <div class="container-fluid">
                @include('errors.validate')
                @include('flash::message')
                    @yield('content')
        </div>
        @yield('footer')
        <script>
            $('div.alert').not('.alert-important').delay(6000).slideUp(300);
        </script>
    </body>
</html>

