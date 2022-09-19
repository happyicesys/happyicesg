var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ui.bootstrap.datetimepicker',
                                    'datePicker'
                                ]);

    function dailyReportController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            profile_id: '',
            date_from: moment().startOf('month').format('YYYY-MM-DD'),
            date_to: moment().format('YYYY-MM-DD'),
            cust_id: '',
            id_prefix: '',
            company: '',
            custcategory: [],
            exclude_custcategory: '',
            status: 'Delivered',
            tag: '',
            driver: '',
            user: '',
            person_active: [],
            is_commission: '0',
            pageNum: 100,
            sortBy: true,
            sortName: '',
            edited: false,
        }

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_daily_report').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Daily Report"+ now + ".xls");
        };

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1, false);
        }

            // when hitting search button
        $scope.searchDB = function () {
            $scope.search.edited = true;
        }

        // search button transaction index
        $scope.onSearchButtonClicked = function (event) {
            event.preventDefault();
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
            $scope.search.edited = false;
        }

        $scope.dateFromChanged = function(date){
            if(date){
                $scope.search.date_from = moment(new Date(date)).format('YYYY-MM-DD');
                $scope.searchDB();
            }
        }

        $scope.dateToChanged = function(date){
            if(date){
                $scope.search.date_to = moment(new Date(date)).format('YYYY-MM-DD');
                $scope.searchDB();
            }
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.driverInit = function(driver) {
            $scope.search.driver = driver;
            $scope.search.custcategory = ['55'];
            $scope.search.exclude_custcategory = '1';
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/dailyreport/index/1?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.alldeals.data){
                    $scope.alldata = data.alldeals.data;
                    $scope.totalCount = data.alldeals.total;
                    $scope.currentPage = data.alldeals.current_page;
                    $scope.indexFrom = data.alldeals.from;
                    $scope.indexTo = data.alldeals.to;
                }else{
                    $scope.alldata = data.alldeals;
                    $scope.totalCount = data.alldeals.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.alldeals.length;
                }
                $scope.subtotal = data.subtotal;
                $scope.today_total = data.today_total;
                $scope.yesterday_total = data.yesterday_total;
                $scope.last_two_day_total = data.last_two_day_total;
                $scope.totalcommission = data.totalcommission;
                $scope.driver = data.driver;
                $scope.extra_location_total = data.extra_location_total;
                $scope.online_location_total = data.online_location_total;
                $scope.spinner = false;
            });
        }
    }

    function driverLocationCountController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            profile_id: '',
            date_from: moment().startOf('month').format('YYYY-MM-DD'),
            date_to: moment().format('YYYY-MM-DD'),
            status: 'Delivered',
            driver: '',
            pageNum: 100,
            sortBy: true,
            sortName: '',
            edited: false,
        }
        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_driver_location_count').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Driver Location Count"+ now + ".xls");
        };

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1, false);
        }

        // when hitting search button
        $scope.searchDB = function () {
            $scope.search.edited = true;
        }

        // search button transaction index
        $scope.onSearchButtonClicked = function (event) {
            event.preventDefault();
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
            $scope.search.edited = false;
        }

        $scope.dateFromChanged = function(date){
            if(date){
                $scope.search.date_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
        }

        $scope.dateToChanged = function(date){
            if(date){
                $scope.search.date_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        }

        $scope.updateLocationCount = function(result) {
            $http.post('/api/dailyreport/driver-location-count/update', result).success(function(data) {
            });
        }

        $scope.driverInit = function(driver) {
            $scope.search.driver = driver;
        }


        $scope.onButtonClicked = function(result, status) {
            $http.post('/api/dailyreport/driver-location-count/update/' + status, result).success(function(data) {
                var index = $scope.alldata.indexOf(result);
                $scope.alldata.splice(index, 1, data);

            });
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/dailyreport/location-count?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.alldeals.data){
                    $scope.alldata = data.alldeals.data;
                    $scope.totalCount = data.alldeals.total;
                    $scope.currentPage = data.alldeals.current_page;
                    $scope.indexFrom = data.alldeals.from;
                    $scope.indexTo = data.alldeals.to;
                }else{
                    $scope.alldata = data.alldeals;
                    $scope.totalCount = data.alldeals.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.alldeals.length;
                }
                $scope.driver = data.driver;
                $scope.spinner = false;
            });
        }
    }

    function accountManagerPerformanceController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            profile_id: '',
            current_month: moment().month()+1 + '-' + moment().year(),
            status: 'Delivered',
            cust_id: '',
            company: '',
            custcategory: [],
            exclude_custcategory: '',
            account_manager: '',
            zones: [],
            is_commission: '0',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        // init page load
        getPage();

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_account_manager').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Driver Location Count"+ now + ".xls");
        };

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1, false);
        }

          // when hitting search button
        $scope.searchDB = function(){
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
        }

        $scope.merchandiserInit = function(userId) {
            $scope.search.account_manager = userId;
        }
/*
        $scope.getGroupedBackgroundColor = function(index) {
            if(index/ 2 == 0) {
                return '#F1F1F1'
            }
            'background-color': ($index/ 2 == 0) ? '#F1F1F1' : ''
        } */

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/dailyreport/account-manager-performance?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                $scope.alldata = data;
/*
                if(data.alldeals.data){
                    $scope.alldata = data.alldeals.data;
                    $scope.totalCount = data.alldeals.total;
                    $scope.currentPage = data.alldeals.current_page;
                    $scope.indexFrom = data.alldeals.from;
                    $scope.indexTo = data.alldeals.to;
                }else{
                    $scope.alldata = data.alldeals;
                    $scope.totalCount = data.alldeals.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.alldeals.length;
                } */
                $scope.spinner = false;
            });
        }
    }


app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            if(input) {
                return $filter('date')(new Date(input), format);
            }else {
                return '';
            }
        };
    }
]);

app.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

app.config(function ($provide){
    $provide.decorator('mFormatFilter', function (){
        return function newFilter(m, format, tz)
        {
            if (!(moment.isMoment(m))) {
                return '';
            }
            return tz ? moment.tz(m, tz).format(format) : m.format(format);
        };
    });
});

app.controller('dailyReportController', dailyReportController);
app.controller('driverLocationCountController', driverLocationCountController);
app.controller('accountManagerPerformanceController', accountManagerPerformanceController);