var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
]);

function cashlessController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        provider_id: '',
        itemsPerPage: 'All',
        sortName: '',
        sortBy: true
    }

    $scope.form = {
        id: '',
        provider_id: '',
        terminal_id: '',
        start_date: '',
    }

    // init page load
    getPage(1);

    angular.element(document).ready(function () {
        $('.select2').select2();
    });

    $scope.exportData = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportableCashless').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "CashlessTerminalRpt" + now + ".xls");
    };

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage);
    };

    $scope.pageNumChanged = function () {
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1)
    };

    $scope.onStartDateChanged = function (date) {
        if (date) {
            $scope.form.start_date = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
    }

    // retrieve page w/wo search
    function getPage(pageNumber) {
        $scope.spinner = true;
        $http.post('/api/cashless/data?page=' + pageNumber, $scope.search).success(function (data) {
            if (data.cashlessTerminals.data) {
                $scope.alldata = data.cashlessTerminals.data;
                $scope.totalCount = data.cashlessTerminals.total;
                $scope.currentPage = data.cashlessTerminals.current_page;
                $scope.indexFrom = data.cashlessTerminals.from;
                $scope.indexTo = data.cashlessTerminals.to;
            } else {
                $scope.alldata = data.cashlessTerminals;
                $scope.totalCount = data.cashlessTerminals.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.cashlessTerminals.length;
            }
            // get total count
            $scope.All = data.cashlessTerminals.length;

            // return total amount
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        }).error(function (data) {

        });
    }

    $scope.createCashlessModal = function () {
        $scope.form = {
            id: '',
            provider_id: '',
            terminal_id: '',
            start_date: '',
        }
    }


    $scope.createCashless = function () {
        $http.post('/api/cashless/create', $scope.form).success(function (data) {
            getPage(1);

            $scope.form = {
                id: '',
                provider_id: '',
                terminal_id: '',
                start_date: '',
            }
        }).error(function (data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeCashless = function (event, id) {
        event.preventDefault();
        var isConfirmDelete = confirm('Are you sure to DELETE this Cashless Terminal?');
        if (isConfirmDelete) {
            $http.delete('/api/cashless/' + id + '/delete').success(function (data) {
                getPage(1);
            });
        } else {
            return false;
        }
    }

    $scope.editCashlessModal = function (cashless) {
        fetchSingleCashlesss(cashless);
        $('.select2').select2();
    }

    function fetchSingleCashlesss(cashless) {
        $scope.form = {
            id: cashless.id,
            provider_id: cashless.provider_id,
            provider_name: cashless.provider_name,
            terminal_id: cashless.terminal_id,
            start_date: cashless.start_date,
        }
    }

    $scope.editCashless = function (id) {
        $http.post('/api/cashless/update/' + id, $scope.form)
            .success(function (data) {
                getPage(1);
            })
            .error(function (data) {
                $scope.errors = data.errors;
                return false;
            });
    }

}

app.filter('delDate', [
    '$filter', function ($filter) {
        return function (input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.controller('cashlessController', cashlessController);