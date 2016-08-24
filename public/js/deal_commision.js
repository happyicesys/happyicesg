var app = angular.module('app', ['ui.bootstrap',
                                'angularUtils.directives.dirPagination',
                                'ui.select',
                                'ngSanitize',
                                'ui.bootstrap.datetimepicker']);

    function dealsController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 5;

        $('.person').select2({
            placeholder:'Select...'
        });

        angular.element(document).ready(function () {

            $scope.onPersonSelected = function (person){

                $http.get('/market/deal/commision/latest/' + person).success(function(transactions){
                    $scope.transactions = transactions;
                });
            }

        });
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}


app.controller('dealsController', dealsController);
app.controller('repeatController', repeatController);
