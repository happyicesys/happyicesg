var app = angular.module('app', [
    'ui.bootstrap', 
    'angularUtils.directives.dirPagination',
    ]);

    function userController($scope, $http){

    $scope.currentPage = 1;
    $scope.itemsPerPage = 10; 
    $scope.currentPage2 = 1;
    $scope.itemsPerPage2 = 10; 
    $scope.currentPage3 = 1;
    $scope.itemsPerPage3 = 10; 
    $scope.currentPage4 = 1;
    $scope.itemsPerPage4 = 10;             

        $http.get('/user/data').success(function(users){
            $scope.users = users;
        });

        $http.get('/freezer/data').success(function(freezers){
            $scope.freezers = freezers;
        });

        $http.get('/accessory/data').success(function(accessories){
            $scope.accessories = accessories;
        });

        $http.get('/payterm/data').success(function(payterms){
            $scope.payterms = payterms;
        });                        

        //delete record
        $scope.confirmDelete = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/user/data/' + id
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

        $scope.confirmDelete2 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/freezer/data/' + id
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

        $scope.confirmDelete3 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/accessory/data/' + id
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

        $scope.confirmDelete4 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/payterm/data/' + id
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
    } 

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

function repeatController2($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage2 - 1) * $scope.itemsPerPage2;
    })
} 

function repeatController3($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage3 - 1) * $scope.itemsPerPage3;
    })
} 

function repeatController4($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage4 - 1) * $scope.itemsPerPage4;
    })
} 
    

app.controller('userController', userController);
app.controller('repeatController', repeatController);
app.controller('repeatController2', repeatController2);
app.controller('repeatController3', repeatController3);
app.controller('repeatController4', repeatController4);

$(function() {
    // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // save the latest tab; use cookies if you like 'em better:
        localStorage.setItem('lastTab', $(this).attr('href'));
    });

    // go to the latest tab, if it exists:
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
});






