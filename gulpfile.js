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
            '../bower/angularjs-datepicker/src/css/angular-datepicker.css',
            '../bower/angular-datepicker/dist/angular-datepicker.css',
            '../bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'app.css',
            ])
        .scripts([
            'lodash.js',
            'jquery.min.js',
            '../bower/moment/min/moment.min.js',
            'bootstrap-js/bootstrap.min.js',
            'angular.js',
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
            'tableExport/libs/FileSaver/FileSaver.min.js',
            'tableExport/libs/jsPDF/jspdf.min.js',
            'tableExport/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js',
            'tableExport/libs/html2canvas/html2canvas.min.js',
            'tableExport/tableExport.js',
            '../bower/angularjs-datepicker/src/js/angular-datepicker.js',
            '../bower/angular-ui-select2/src/select2.js',
            '../bower/angular-datepicker/dist/angular-datepicker.js',
            'vue/vue.js',
            'vue/vue-resource.js',
            'axios/axios.js',
            '../bower/angular-eonasdan-datetimepicker/dist/angular-eonasdan-datetimepicker.min.js',
            ])
        .version(['public/css/all.css', 'public/js/all.js']);
});
