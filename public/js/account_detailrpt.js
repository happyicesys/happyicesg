var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ui.bootstrap.datetimepicker',
                                    'datePicker'
                                ]);

    function custDetailController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            profile_id: '',
            delivery_from: $scope.today,
            payment_from: '',
            cust_id: '',
            delivery_to: $scope.today,
            payment_to: '',
            company: '',
            status: '',
            person_id: '',
            payment: '',
            custcategory: '',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        $scope.updated_at = '';
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.onDeliveryFromChanged = function(date){
            if(date){
                $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            // $scope.searchDB();
        }
        $scope.onPaymentFromChanged = function(date){
            if(date){
                $scope.search.payment_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            // $scope.searchDB();
        }
        $scope.onDeliveryToChanged = function(date){
            if(date){
                $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            // $scope.searchDB();
        }
        $scope.onPaymentToChanged = function(date){
            if(date){
                $scope.search.payment_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            // $scope.searchDB();
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            // $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            // $scope.searchDB();
        }

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_custdetail').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Customer Details (Account)"+ now + ".xls");
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

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/account/custdetail?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
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
    }

    function custOutstandingController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            profile_id: '',
            current_month: moment().month()+1 + '-' + moment().year(),
            cust_id: '',
            company: '',
            status: 'Delivered',
            person_id: '',
            payment: 'Owe',
            custcategory: '',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        $scope.updated_at = '';
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_custoutstanding').innerHTML], {
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
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

          // when hitting search button
        $scope.searchDB = function(){
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
        }

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
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
                $scope.totals = data.totals;
                $scope.spinner = false;
            });
        }
    }

    function custPayDetailController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            profile_id: '',
            payment_from: $scope.today,
            delivery_from: '',
            cust_id: '',
            payment_to: $scope.today,
            delivery_to: '',
            company: '',
            payment: '',
            status: 'Delivered',
            person_id: '',
            pay_method: '',
            custcategory: '',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        $scope.updated_at = '';
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.onDeliveryFromChanged = function(date){
            if(date){
                $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onPaidFromChanged = function(date){
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
        $scope.onPaidToChanged = function(date){
            if(date){
                $scope.search.payment_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_paydetail').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Payment Detail(Account)"+ now + ".xls");
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

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/account/paydetail?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
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
                $scope.total_inv_amount = data.total_inv_amount;
                $scope.total_gst = data.total_gst;
                $scope.spinner = false;
            });
        }
    }

    function custPaySummaryController($scope, $http) {
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.sortBy = true;
        $scope.sortName = '';
        $scope.headerTemp = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            profile_id: '',
            payment_from: $scope.today,
            payment_to: $scope.today,
            pay_method: '',
            sortBy: true,
            sortName: '',
            itemsPerPage: 100
        }
        // init page load
        // getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();

            $('#checkAll').change(function(){
                var all = this;
                $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
            });
        });

        $scope.onPaymentFromChanged = function(date){
            if(date){
                $scope.search.payment_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onPaymentToChanged = function(date){
            if(date){
                $scope.search.payment_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onBankinFromChanged = function(date){
            if(date){
                $scope.search.bankin_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onBankinToChanged = function(date){
            if(date){
                $scope.search.bankin_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_paysummary').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Payment Summary(Account)"+ now + ".xls");
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
        // $scope.searchDB = function(){
        //     $scope.search.sortName = '';
        //     $scope.search.sortBy = true;
        //     getPage(1, false);
        // }

            // when hitting search button
        $scope.searchDB = function () {
            $scope.search.edited = true;
        }

        // search button transaction index
        $scope.onSearchButtonClicked = function (event) {
            event.preventDefault();
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1);
            $scope.search.edited = false;
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/account/paysummary?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
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
                $scope.totals = data.totals;

                $scope.spinner = false;
            });
        }

        $scope.verifyPaysummary = function(event, transaction, is_verified) {
            event.preventDefault();
            $http.post('/api/detailrpt/account/paysummary/verify', {
                bankin_date: transaction.bankin_date,
                paid_at: transaction.payreceived_date,
                pay_method: transaction.pay_method,
                profile_id: transaction.profile_id,
                remark: transaction.remark,
                is_verified: is_verified
            }).success(function(data) {
                getPage(1, false);
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

app.controller('custDetailController', custDetailController);
app.controller('custOutstandingController', custOutstandingController);
app.controller('custPayDetailController', custPayDetailController);
app.controller('custPaySummaryController', custPaySummaryController);