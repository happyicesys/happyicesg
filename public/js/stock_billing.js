var app = angular.module('app', [
    // 'ui.bootstrap',
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker',
    'ui.bootstrap.datetimepicker',
    'datePicker'
]);

function stockBillingController($scope, $http) {
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
        driver: '',
        exACategory: '',
        custcategory: [],
        exclude_custcategory: '',
        is_inventory: '',
        is_commission: '',
        pageNum: 100,
        sortBy: true,
        sortName: '',
        edited: false
    }
    $scope.updated_at = '';
    // $scope.internal_billing_div = false;
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function ($event) {
        $event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable_stockbilling').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Stock Billing" + now + ".xls");
    };
    /*
            $scope.enableInternalBilling = function() {
                $scope.internal_billing_div = !$scope.internal_billing_div;
            }*/

    $scope.onDeliveryFromChanged = function (date) {
        if (date) {
            $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onDeliveryToChanged = function (date) {
        if (date) {
            $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function () {
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1, false)
    };

    $scope.onPrevSingleClicked = function (scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextSingleClicked = function (scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.edited = true;
        // $scope.search.sortName = '';
        // $scope.search.sortBy = true;
        // getPage(1, false);
    }

    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1, false);
    }

    $scope.onExACategoryChanged = function () {
        if ($scope.search.exACategory) {
            $scope.search.custcategory.push("2");
            $scope.search.exclude_custcategory = true;
        } else {
            $scope.search.custcategory.splice($scope.search.custcategory.indexOf("2"), 1);
            $scope.search.exclude_custcategory = false;
        }
        $scope.searchDB();
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/stock/billing?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.deals.data) {
                $scope.alldata = data.deals.data;
                $scope.totalCount = data.deals.total;
                $scope.currentPage = data.deals.current_page;
                $scope.indexFrom = data.deals.from;
                $scope.indexTo = data.deals.to;
            } else {
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
            $scope.total_gross_profit_percent = data.totals.total_gross_profit_percent.toFixed(2);
            $scope.total_sf_fee = data.totals.total_sf_fee.toFixed(2);
            $scope.total_commission = data.totals.total_commission.toFixed(2);
            $scope.total_gross_after_sf_fee = data.totals.total_gross_after_sf_fee.toFixed(2);
            $scope.total_gross_after_sf_fee_percent = data.totals.total_gross_after_sf_fee_percent.toFixed(2);
            $scope.spinner = false;
        });
    }
}



app.filter('delDate', [
    '$filter', function ($filter) {
        return function (input, format) {
            if (input) {
                return $filter('date')(new Date(input), format);
            } else {
                return '';
            }
        };
    }
]);

app.filter('capitalize', function () {
    return function (input) {
        return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

app.config(function ($provide) {
    $provide.decorator('mFormatFilter', function () {
        return function newFilter(m, format, tz) {
            if (!(moment.isMoment(m))) {
                return '';
            }
            return tz ? moment.tz(m, tz).format(format) : m.format(format);
        };
    });
});

app.controller('stockBillingController', stockBillingController);