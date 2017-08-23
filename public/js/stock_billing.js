var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ui.bootstrap.datetimepicker',
                                    'datePicker'
                                ]);

    function stockBillingController($scope, $http){
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
            custcategory_id: '',
            is_inventory: '1',
            is_commission: '0',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        $scope.updated_at = '';
        // $scope.internal_billing_div = false;
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
        });

        $scope.exportData = function ($event) {
            $event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_stockbilling').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Stock Billing"+ now + ".xls");
        };
/*
        $scope.enableInternalBilling = function() {
            $scope.internal_billing_div = !$scope.internal_billing_div;
        }*/

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
            getPage(1, false);
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/stock/billing?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
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
                // return fixed total amount
                $scope.total_qty = data.totals.total_qty.toFixed(2);
                $scope.total_costs = data.totals.total_costs.toFixed(2);
                $scope.total_sell_value = data.totals.total_sell_value.toFixed(2);
                $scope.total_gross_profit = data.totals.total_gross_profit.toFixed(2);
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

app.controller('stockBillingController', stockBillingController);