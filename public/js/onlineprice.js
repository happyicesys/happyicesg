var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize']);

    function personEditController($scope, $http){

        $http.get('/item/data').success(function(items){
            $scope.items = items;
        });

        $http.get('/person/price/'+ $('#person_id').val()).success(function(prices){
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

        $http.get('/person/specific/data/'+ $('#person_id').val()).success(function(person){
            $scope.personData = person;
            $scope.noteModel = person.note;

            $scope.getRetailChange = function(retailModel){
                $scope.afterChange = (retailModel * person.cost_rate/100).toFixed(2);
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
