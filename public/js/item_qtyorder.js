var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

function itemOrderqtyController($scope, $q, $http){
    $http.get('/api/item/qtyorder/' + $('#item_id').val()).success(function(data){
        $scope.transactions = data.transactions;
    });
}

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.controller('itemOrderqtyController', itemOrderqtyController);
