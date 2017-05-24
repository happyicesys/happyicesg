var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ui.bootstrap.datetimepicker',
                                    'datePicker'
                                ]);

    function invbreakdownSummaryController($scope, $http){
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
        $scope.search = {
            profile_id: '',
            delivery_from: moment().startOf('month').format('YYYY-MM-DD'),
            delivery_to: moment().format("YYYY-MM-DD"),
            status: 'Delivered',
            cust_id: '',
            company: '',
            person_id: '',
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
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_invbreakdownsummary').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Invoice Breakdown Summary"+ now + ".xls");
        };

        $scope.onDeliveryFromChanged = function(date){
            if(date){
                $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.onDeliveryToChanged = function(date){
            if(date){
                $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

          // when hitting search button
        $scope.searchDB = function(){
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
        }

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            console.log($scope.search.sortName);
            console.log($scope.search.sortBy);
            getPage(1, false);
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/invbreakdown/summary?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.deals.data){
                    $scope.alldata = data.deals.data;
                    $scope.totalCount = data.deals.total;
                    $scope.currentPage = data.deals.current_page;
                    $scope.indexFrom = data.deals.from;
                    $scope.indexTo = data.deals.to;
                }else{
                    $scope.alldata = data.deals;
                    $scope.totalCount = data.deals.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.deals.length;
                }
                // get total count
                $scope.All = data.deals.length;
                console.log(data);
                // return fixed total amount
                $scope.grand_total = data.fixedtotals.grand_total.toFixed(2);
                $scope.taxtotal = data.fixedtotals.taxtotal.toFixed(2);
                $scope.subtotal = data.fixedtotals.subtotal.toFixed(2);
                $scope.total_gross_money = data.fixedtotals.total_gross_money.toFixed(2);
                $scope.total_gross_percent = data.fixedtotals.total_gross_percent.toFixed(2);

                $scope.avg_grand_total = data.dynamictotals.avg_grand_total.toFixed(2);
                $scope.avg_subtotal = data.dynamictotals.avg_subtotal.toFixed(2);
                $scope.avg_cost = data.dynamictotals.avg_cost.toFixed(2);
                $scope.avg_gross_money = data.dynamictotals.avg_gross_money.toFixed(2);
                $scope.avg_gross_percent = data.dynamictotals.avg_gross_percent.toFixed(2);
                $scope.avg_vending_piece_price = data.dynamictotals.avg_vending_piece_price.toFixed(2);
                $scope.avg_vending_monthly_rental = data.dynamictotals.avg_vending_monthly_rental.toFixed(2);
                $scope.avg_sales_qty = data.dynamictotals.avg_sales_qty.toFixed(2);
                $scope.avg_sales_avg_day = data.dynamictotals.avg_sales_avg_day.toFixed(2);
                $scope.avg_difference = data.dynamictotals.avg_difference.toFixed(2);
                $scope.avg_vm_stock_value = data.dynamictotals.avg_vm_stock_value.toFixed(2);

                $scope.total_grand_total = data.dynamictotals.total_grand_total.toFixed(2);
                $scope.total_subtotal = data.dynamictotals.total_subtotal.toFixed(2);
                $scope.total_gsttotal = data.dynamictotals.total_gsttotal.toFixed(2);
                $scope.total_cost = data.dynamictotals.total_cost.toFixed(2);
                $scope.total_gross_money = data.dynamictotals.total_gross_money.toFixed(2);
                $scope.total_gross_percent = data.dynamictotals.total_gross_percent.toFixed(2);
                $scope.total_owe = data.dynamictotals.total_owe.toFixed(2);
                $scope.total_paid = data.dynamictotals.total_paid.toFixed(2);
                $scope.total_vending_monthly_rental = data.dynamictotals.total_vending_monthly_rental.toFixed(2);
                $scope.total_sales_qty = data.dynamictotals.total_sales_qty.toFixed(2);
                $scope.total_difference = data.dynamictotals.total_difference.toFixed(2);
                $scope.total_vm_stock_value = data.dynamictotals.total_vm_stock_value.toFixed(2);
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

app.controller('invbreakdownSummaryController', invbreakdownSummaryController);