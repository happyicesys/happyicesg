var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function customerController($scope, $http){

        $scope.currentPage = 1;
        $scope.itemsPerPage = 50;


        angular.element(document).ready(function () {

            $http.get('/market/customer/data').success(function(customers){
                $scope.customers = customers;
                $scope.All = customers.length;
            });

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete the entry');
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/person/data/' + id
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

            $scope.exportData = function () {
                var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                    type: "application/vnd.ms-excel;charset=charset=utf-8"
                });
                var now = Date.now();
                saveAs(blob, "CustomerRpt"+ now + ".xls");
            };
        });
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('customerController', customerController);
app.controller('repeatController', repeatController);
