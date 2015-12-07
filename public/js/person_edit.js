var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize']);

    function personEditController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;  

        angular.element(document).ready(function () {

            $http.get('/person/transac/'+ $('#person_id').val()).success(function(transactions){
                console.log(transactions);
                $scope.transactions = transactions;       
            });

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/transaction/data/' + id
                    })
                    .success(function(data){
                        console.log(data);
                        location.reload();
                    })
                    .error(function(data){
                        console.log(data);
                        alert('Unable to delete');
                    })
                }else{
                    return false;
                }
            } 
        });
    }  

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    

app.controller('personEditController', personEditController);
app.controller('repeatController', repeatController);
