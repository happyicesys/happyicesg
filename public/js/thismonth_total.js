var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

function productDetailMonthThismonthController($scope, $q, $http){

    $scope.alldata = [];
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        sortName: '',
        sortBy: true
    }
    getPage(1);


    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_thismonth_total').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "This Month Total (Product Month)"+ now + ".xls");
    };

    $scope.sortTable = function(sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = ! $scope.search.sortBy;
        getPage(1);
    }

    function getPage(pageNumber){
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/'
                    + $('#item_id').val()
                    + '/thismonth?current_from=' + $('#current_from').val()
                    + '&current_to=' + $('#current_to').val(),
                    $scope.search)
            .success(function(data) {
                $scope.alldata = data.transactions;
                $scope.totalCount = data.transactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.transactions.length;
            });
        $scope.spinner = false;
    }
}

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.controller('productDetailMonthThismonthController', productDetailMonthThismonthController);
