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
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>

            Vue.http.interceptors.push((request, next) => {
                // request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;
                request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken);

                next();
            });
        </script>
    </body>
</html>

