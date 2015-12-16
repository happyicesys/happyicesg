var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function personController($scope, $http){

        $scope.currentPage = 1;
        $scope.itemsPerPage = 30;         

        angular.element(document).ready(function () {
       
            $http.get('/person/data').success(function(people){
            $scope.people = people;
            });

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
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
        });
    }  

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    

app.controller('personController', personController);
app.controller('repeatController', repeatController);
