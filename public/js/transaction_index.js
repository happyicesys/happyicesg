var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker']);

    function transController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 70;
        $scope.indexData = {};
        $scope.dataCache = false;

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "TransactionRpt"+ now + ".xls");
        };

        var now = moment();
        $scope.today = now.format("YYYY-MM-DD");

        angular.element(document).ready(function () {

            getIndex();

            function getIndex(){

                $http.post('/transaction/data', $scope.indexData).success(function(transactions){

                    $scope.transactions = transactions;

                    $scope.All = transactions.length;

                });

            }

            $scope.dateChange = function(date){

                if(date){

                    $scope.delivery_from = moment(date).format("YYYY-MM-DD");
                }

                $scope.indexData = {

                    delivery_from: $scope.delivery_from,

                    delivery_to: $scope.delivery_to,

                    dataCache: $scope.dataCache,

                }

                getIndex();

                $scope.indexData['dataCache'] = true;
            }

            $scope.dateChange2 = function(date){

                $scope.search.updated_at = moment(date).format("YYYY-MM-DD");

            }

            $scope.dateChange3 = function(date){

                if(date){

                    $scope.delivery_to = moment(date).format("YYYY-MM-DD");
                }

                $scope.indexData = {

                    delivery_from: $scope.delivery_from,

                    delivery_to: $scope.delivery_to,

                    dataCache: $scope.dataCache,

                }

                getIndex();

                $scope.indexData['dataCache'] = true;
            }

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/transaction/data/' + id
                    })
                    .success(function(data){
                        location.reload();
                    })
                    .error(function(data){
                        alert('Unable to delete');
                    })
                }else{
                    return false;
                }
            }
        });
    }

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}


app.controller('transController', transController);
app.controller('repeatController', repeatController);
