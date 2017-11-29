var app = angular.module('app', ['ui.bootstrap',
                                'angularUtils.directives.dirPagination',
                                'ui.select',
                                'ngSanitize',
                                'ui.bootstrap.datetimepicker']);

    function ftransController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;

        $('.person').select2({
            placeholder:'Select...'
        });

        angular.element(document).ready(function () {
            $scope.onPersonSelected = function (person){
                $http.get('/franchisee/person/latest/' + person).success(function(ftransactions){
                    $scope.ftransactions = ftransactions;
                });
            }

        });
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            if(input) {
                return $filter('date')(new Date(input), format);
            }else {
                return '';
            }
        };
    }
]);

app.controller('ftransController', ftransController);
app.controller('repeatController', repeatController);
