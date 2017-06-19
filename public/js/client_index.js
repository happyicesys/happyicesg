var app = angular.module('app', ['angularUtils.directives.dirPagination']);

function clientMainController($scope, $http){
    $scope.productModel = false;
    $scope.productText = 'Show All Products';
    $scope.currentPage = 1;
    $scope.itemsPerPage = 3;
    $scope.modalSrc = '';

    $http.get('/client/item').success(function(products){
        $scope.products = products;
        $scope.itemsPerPage = products.length;
/*
        $scope.showAllProduct = function(){
            $scope.productModel = ! $scope.productModel;
            if($scope.productModel){

                // $scope.productText = 'Hide Products';
                $scope.itemsPerPage = products.length;

            }else{

                $scope.itemsPerPage = products.length;
                // $scope.productText = 'Show All Products';
                // $scope.itemsPerPage = 3;
            }

        }*/
    });

    $scope.activateSrc = function(product) {
        $scope.modalSrc = product.desc_imgpath;
    }
}

app.controller('clientMainController', clientMainController);

