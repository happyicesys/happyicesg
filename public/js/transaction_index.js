var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function transController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;  

        angular.element(document).ready(function () {

            $http.get('/transaction/data').success(function(transactions){
                $scope.transactions = transactions;

/*            //search company null bug
            $scope.clear = function(){
                console.log($scope.search.person.company.length);
                if($scope.search.person.company.length == 0){
                    console.log('yes');
                    // delete $scope.search.person.company;
                    $scope.transactions = [];
                    $scope.transactions = transactions;
                }
            }   */          
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

app.controller('transController', transController);
app.controller('repeatController', repeatController);
