var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.sass('app.scss', 'resources/assets/css/app.css')
        .styles([
            'bootstrap-css/bootstrap.css',
            'bootstrap-css/bootstrap-theme.css',
            'select2.min.css',
            '../bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'dropzone.css',
            '../bower/ui-select/dist/select.min.css',
            'angular-bootstrap-datetimepicker.css',
            'animate.min.css',
            'app.css',
            ])
        .scripts([
            'jquery.min.js',
            '../bower/moment/min/moment.min.js',
            'bootstrap-js/bootstrap.min.js',
            'angular.min.js',
            'angular-bootstrap.min.js',
            '../bower/angular-sanitize/angular-sanitize.min.js',
            'dirPagination.js',
            'select2.min.js',
            '../bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            'dropzone.js',
            'fixedScroll.js',
            '../bower/ui-select/dist/select.min.js',
            'filesaver.js',
            'moment.js',
            'angular-bootstrap-datetimepicker.js',
            ])
        .version(['public/css/all.css', 'public/js/all.js']);
});
