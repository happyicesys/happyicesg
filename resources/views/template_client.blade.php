<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header_client')
    </head>

    <body>
        @include('partials.nav_client')
        <div class="container-fluid">
            <div class="col-md-12">
            @include('errors.validate')
            @include('flash::message')
                @yield('content')
            </div>
        </div>
        @yield('footer')

    </body>
</html>

