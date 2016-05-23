var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize']);

    function setupController($scope, $http){

        $http.get('/item/data').success(function(items){
            $scope.items = items;
        });

        $http.get('/market/setup/price').success(function(prices){
            $scope.prices = prices;

            $scope.getRetailInit = function(item_id){
                var retailNum = 0;
                for(var i = 0; i < $scope.prices.length; i ++){
                    var price = $scope.prices[i];
                    if(item_id == price.item_id){
                        retailNum = price.retail_price;
                        return retailNum;
                    }
                }
            }

            $scope.getQuoteInit = function(item_id){
                var quoteNum = 0;
                for(var i = 0; i < $scope.prices.length; i ++){
                    var price = $scope.prices[i];
                    if(item_id == price.item_id){
                        quoteNum = price.quote_price;
                        return quoteNum;
                    }
                }
            }
        });

    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('setupController', setupController);
app.controller('repeatController', repeatController);
