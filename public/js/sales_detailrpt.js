var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'datePicker'
                                ]);

    function custDetailController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.sortBy = true;
        $scope.sortName = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            profile_id: '',
            current_month: moment().month()+1 + '-' + moment().year(),
            id_prefix: '',
            cust_id: '',
            company: '',
            pageNum: 100,
        }
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_custdetail').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Customer Details (Sales)"+ now + ".xls");
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

          // when hitting search button
        $scope.searchDB = function(){
            $scope.sortName = '';
            $scope.sortBy = ''
            getPage(1, false);
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/sales/custdetail?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.transactions.data){
                    $scope.alldata = data.transactions.data;
                    $scope.totalCount = data.transactions.total;
                    $scope.currentPage = data.transactions.current_page;
                    $scope.indexFrom = data.transactions.from;
                    $scope.indexTo = data.transactions.to;
                }else{
                    $scope.alldata = data.transactions;
                    $scope.totalCount = data.transactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.transactions.length;
                }
                $scope.All = data.transactions.length;
                $scope.total_amount = data.total_amount;
                $scope.spinner = false;
            });
        }
    }
/*
    function custSummaryController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.sortBy = true;
        $scope.sortName = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            profile_id: '',
            delivery_from: '',
            payment_from: '',
            cust_id: '',
            delivery_to: $scope.today,
            payment_to: '',
            company: '',
            status: 'Delivered',
            person_id: '',
            payment: 'Owe'
        }
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
        });

        $scope.onDeliveryFromChanged = function(date){
            if(date){
                $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onPaymentFromChanged = function(date){
            if(date){
                $scope.search.payment_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onDeliveryToChanged = function(date){
            if(date){
                $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onPaymentToChanged = function(date){
            if(date){
                $scope.search.payment_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }


        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Outstanding (Account)"+ now + ".xls");
        };

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            if($.isEmptyObject($scope.datasetTemp)){
                $scope.datasetTemp = {
                    pageNum: $scope.itemsPerPage
                }
            }else{
                $scope.datasetTemp['pageNum'] = $scope.itemsPerPage;
            }
            getPage(1, false);
        };

          // when hitting search button
        $scope.searchDB = function(){
            $scope.sortName = '';
            $scope.sortBy = '';
            getPage(1, false);
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/account/outstanding?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.transactions.data){
                    $scope.alldata = data.transactions.data;
                    $scope.totalCount = data.transactions.total;
                    $scope.currentPage = data.transactions.current_page;
                    $scope.indexFrom = data.transactions.from;
                    $scope.indexTo = data.transactions.to;
                }else{
                    $scope.alldata = data.transactions;
                    $scope.totalCount = data.transactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.transactions.length;
                }
                // get total count
                $scope.All = data.transactions.length;

                // return total amount
                $scope.total_amount = data.total_amount;
                $scope.spinner = false;
            });
        }
    }*/

    function productDayDetailController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.sortBy = true;
        $scope.sortName = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            cust_id: '',
            delivery_from: $scope.today,
            product_id: '',
            company: '',
            delivery_to: $scope.today,
            product_name: '',
            pageNum: 100,
        }
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_productday').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Product Detail(Day)"+ now + ".xls");
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

          // when hitting search button
        $scope.searchDB = function(){
            $scope.sortName = '';
            $scope.sortBy = '';
            getPage(1, false);
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/sales/productday?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.transactions.data){
                    $scope.alldata = data.transactions.data;
                    $scope.totalCount = data.transactions.total;
                    $scope.currentPage = data.transactions.current_page;
                    $scope.indexFrom = data.transactions.from;
                    $scope.indexTo = data.transactions.to;
                }else{
                    $scope.alldata = data.transactions;
                    $scope.totalCount = data.transactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.transactions.length;
                }
                $scope.All = data.transactions.length;
                $scope.total_amount = data.total_amount;
                $scope.total_qty = data.total_qty;
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

app.controller('custDetailController', custDetailController);
/*app.controller('custOutstandingController', custOutstandingController);*/
app.controller('productDayDetailController', productDayDetailController);