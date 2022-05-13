var app = angular.module('app', [
    // 'ui.bootstrap',
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker',
    'ui.bootstrap.datetimepicker',
    'datePicker'
]);

function invbreakdownDetailv2Controller($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        excludeCustCat: '',
        custcategories: [],
        custcategoryGroups: [],
        actives: [],
        delivery_from: moment().format("YYYY-MM-DD"),
        delivery_to: moment().format("YYYY-MM-DD"),
        statuses: ['Delivered'],
        personTags: [],
        pageNum: 'All',
        sortBy: true,
        sortName: ''
    }
    $scope.updated_at = '';
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select...'
        });
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable_invbreakdownDetailv2').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Invoice Breakdown Detailv2" + now + ".xls");
    };

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

    $scope.dateChanged = function (modelName, date) {
        $scope.form[modelName] = moment(new Date(date)).format('YYYY-MM-DD');
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

    $scope.onPrevDateClicked = function (scope_from, scope_to) {
        $scope.search[scope_from] = moment(new Date($scope.search[scope_from])).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.search[scope_to] = moment(new Date($scope.search[scope_to])).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onTodayDateClicked = function (scope_from, scope_to) {
        $scope.search[scope_from] = moment().format('YYYY-MM-DD');
        $scope.search[scope_to] = moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextDateClicked = function (scope_from, scope_to) {
        $scope.search[scope_from] = moment(new Date($scope.search[scope_from])).add(1, 'days').format('YYYY-MM-DD');
        $scope.search[scope_to] = moment(new Date($scope.search[scope_to])).add(1, 'days').format('YYYY-MM-DD');
        $scope.searchDB();
    }

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
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
    }

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1, false);
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/invbreakdown/detailv2?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
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
            $scope.totals = data.totals;
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

app.controller('invbreakdownDetailv2Controller', invbreakdownDetailv2Controller);