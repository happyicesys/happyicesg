<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header')
    </head>

    <body>
        @include('partials.nav')
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="col-md-10 col-md-offset-2 main">
                @include('errors.validate')
                @include('flash::message')
                    @yield('content')
                </div>
            </div>
        </div>
        @yield('footer')
        <script>
            $('div.alert').delay(3000).slideUp(300);
        </script>
    </body>
</html>

