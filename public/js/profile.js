var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function profileController($scope, $http){

    $scope.currentPage = 1;
    $scope.itemsPerPage = 30;        
        $http.get('/profile/data').success(function(profiles){
            $scope.profiles = profiles;
            $scope.All = profiles.length;
        });

        //delete record
        $scope.confirmDelete = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/api/profile/' + id + '/destroy'
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
    }  

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    

app.controller('profileController', profileController);
app.controller('repeatController', repeatController);
