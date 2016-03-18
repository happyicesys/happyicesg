var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker']);

function clientMainController($scope, $http){
    $scope.productModel = false;
    $scope.productText = 'Show All Products';
    $scope.currentPage = 1;
    $scope.itemsPerPage = 3;

    $http.get('/client/item').success(function(products){
        $scope.products = products;

        $scope.showAllProduct = function(){
            $scope.productModel = ! $scope.productModel;
            if($scope.productModel){

                $scope.productText = 'Hide Products';
                $scope.itemsPerPage = products.length;

            }else{

                $scope.productText = 'Show All Products';
                $scope.itemsPerPage = 3;
            }

        }
    });
}

app.controller('clientMainController', clientMainController);

