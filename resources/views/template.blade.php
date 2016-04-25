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
    </body>
</html>

