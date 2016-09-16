var app = angular.module('app', ['ui.bootstrap',
                                'angularUtils.directives.dirPagination',
                                'ui.select',
                                'ngSanitize',
                                'ui.bootstrap.datetimepicker']);

    function transCustomerController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;
        $scope.members = [];
        $scope.chosen_member = '';

        $('.select').select2({
            placeholder: 'Select..'
        });

        angular.element(document).ready(function() {
            $scope.onPersonSelected = function (data){
                var person = angular.fromJson(data);
                var id = person.id;
                $scope.chosen_member = person.name;
                $http.get('/api/market/customer/' + id).success(function(customers){
                    $scope.customers = customers;
                });
                $http.get('/api/market/exclude/descmember?manager_id=' + $('#member_id').val() + '&desc_id=' + id).success(function(members){
                    $scope.members = members;
                });
            }
        });
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('transCustomerController', transCustomerController);
app.controller('repeatController', repeatController);
