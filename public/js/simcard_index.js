var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
]);

function simcardController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        phone_no: '',
        telco_name: '',
        itemsPerPage: 100,
        sortName: '',
        sortBy: true
    }

    $scope.form = {
        id: '',
        phone_no: '',
        telco_name: '',
        simcard_no: '',
        vending_id: '',
        serial_no: ''
    }

    // init page load
    getPage(1);

    angular.element(document).ready(function () {
        $('.select2').select2();
    });

    $scope.exportData = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "SimcardRpt" + now + ".xls");
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
        $http.post('/api/simcard/data?page=' + pageNumber, $scope.search).success(function (data) {
            if (data.simcards.data) {
                $scope.alldata = data.simcards.data;
                $scope.totalCount = data.simcards.total;
                $scope.currentPage = data.simcards.current_page;
                $scope.indexFrom = data.simcards.from;
                $scope.indexTo = data.simcards.to;
            } else {
                $scope.alldata = data.simcards;
                $scope.totalCount = data.simcards.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.simcards.length;
            }
            // get total count
            $scope.All = data.simcards.length;

            // return total amount
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        }).error(function (data) {

        });
    }

    $scope.createSimcardModal = function () {
        $scope.form = {
            id: '',
            phone_no: '',
            telco_name: '',
            simcard_no: '',
            vending_id: '',
            serial_no: ''
        }
    }


    $scope.createSimcard = function () {
        $http.post('/api/simcard/create', $scope.form).success(function (data) {
            getPage(1);

            $scope.form = {
                id: '',
                phone_no: '',
                telco_name: '',
                simcard_no: '',
                vending_id: '',
                serial_no: ''
            }
        }).error(function (data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeSimcard = function (event, id) {
        event.preventDefault();
        var isConfirmDelete = confirm('Are you sure to DELETE this simcard?');
        if (isConfirmDelete) {
            $http.delete('/api/simcard/' + id + '/delete').success(function (data) {
                getPage(1);
            });
        } else {
            return false;
        }
    }

    $scope.editSimcardModal = function (simcard) {
        fetchSingleSimcard(simcard);
    }

    function fetchSingleSimcard(simcard) {
        $scope.form = {
            id: simcard.id,
            phone_no: simcard.phone_no,
            telco_name: simcard.telco_name,
            simcard_no: simcard.simcard_no,
            vending_id: simcard.vending_id,
            serial_no: simcard.serial_no
        }
    }

    $scope.editSimcard = function () {
        $http.post('/api/simcard/update', $scope.form).success(function (data) {
            getPage(1);
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

app.controller('simcardController', simcardController);