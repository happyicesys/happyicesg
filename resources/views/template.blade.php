<!DOCTYPE html>
<html lang="en">
    <head>
        @include('partials.header')

        <style>
            .ui-select-bootstrap .ui-select-match-text .ui-select-allow-clear {
                padding-right: 2.75px;
            }
            /* @media screen and (max-width: 767px) { */
            .alt-table-responsive {
                width: 100%;
                margin-bottom: 5px;
                overflow-y: hidden;
                overflow-x: auto;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #dddddd;
                -webkit-overflow-scrolling: touch;
            }
            /* } */
            ._720kb-datepicker-calendar-day._720kb-datepicker-today {
                background:#87ceeb;
            }
            ._720kb-datepicker-calendar-day._720kb-datepicker-active, ._720kb-datepicker-calendar-day:hover {
                background: #87ceeb;
            }
        </style>
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

