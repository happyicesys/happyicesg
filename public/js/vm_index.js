var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
]);

function vmController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        vend_id: '',
        cust_id: '',
        company: '',
        custcategory: '',
        itemsPerPage: 100,
        sortName: '',
        sortBy: true
    }

    // init page load
    getPage(1);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "VMRpt" + now + ".xls");
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
        $http.post('/api/vm/data?page=' + pageNumber, $scope.search).success(function (data) {
            if (data.vms.data) {
                $scope.alldata = data.vms.data;
                $scope.totalCount = data.vms.total;
                $scope.currentPage = data.vms.current_page;
                $scope.indexFrom = data.vms.from;
                $scope.indexTo = data.vms.to;
            } else {
                $scope.alldata = data.vms;
                $scope.totalCount = data.vms.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.vms.length;
            }
            // get total count
            $scope.All = data.vms.length;

            // return total amount
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        }).error(function (data) {

        });
    }

    //delete record
    $scope.confirmDelete = function (event, id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/vm/' + id +'/destroy'
            }).success(function (data) {
                location.reload();
            }).error(function (data) {
                alert('Unable to delete');
            })
        } else {
            return false;
        }
    }
}

app.filter('delDate', [
    '$filter', function ($filter) {
        return function (input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.controller('vmController', vmController);