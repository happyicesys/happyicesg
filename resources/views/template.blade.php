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
                    @yield('content')
                </div>
            </div>               
        </div>
        @yield('footer')
    </body>
</html>

