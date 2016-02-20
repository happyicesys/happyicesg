<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header')
    </head>
    
    <body>
        @include('partials.nav_client')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
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

