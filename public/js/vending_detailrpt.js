var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ui.bootstrap.datetimepicker',
                                    'datePicker'
                                ]);

    function generateInvoiceController($scope, $http){
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
            current_month: moment().month() + '-' + moment().year(),
            cust_id: '',
            id_prefix: '',
            company: '',
            custcategory: '',
            status: 'Delivered',
            is_profit_sharing_report: '1',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2({
                placeholder: 'Select...'
            });

            $('#checkAll').change(function(){
                var all = this;
                $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
            });
        });

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_generate_invoice').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "VM Generate Invoice"+ now + ".xls");
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
            console.log(sortName);
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

        $scope.getRowColor = function(transaction) {
            if(transaction) {
                if(transaction.begin_date == transaction.end_date) {
                    return '#98fb98';
                }

                if(transaction.clocker_delta > (transaction.last_clocker_delta * 150/100)) {
                    return '#f68080';
                }

                if(transaction.melted_amount > 100) {
                    return 'yellow';
                }
            }
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/vending/invoice?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
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
                $scope.total_sales = data.totals.total_sales;
                $scope.total_sales_figure = data.totals.total_sales_figure;
                $scope.total_profit_sharing = data.totals.total_profit_sharing;
                $scope.total_rental = data.totals.total_rental;
                $scope.total_utility = data.totals.total_utility;
                $scope.total_payout = data.totals.total_payout;
                $scope.total_gross_profit = data.totals.total_gross_profit;
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

app.controller('generateInvoiceController', generateInvoiceController);