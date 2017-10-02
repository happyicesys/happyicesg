var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

    function priceMatrixController($scope, $http){
        $scope.costrate = {};
        $scope.retailprice = {};
        $scope.quoteprice = {};
        $scope.changeRetailPrice = function(item_id, person_id, retailprice) {
/*            console.log('start');
            console.log(item_id);
            console.log(person_id);
            console.log(retailprice);
            console.log($scope.quoteprice[item_id + '-' + person_id]);
            console.log($scope.costrate[person_id]);
            console.log($scope.retailprice);
            console.log($scope.quoteprice);
            console.log($scope.costrate);
            console.log('end');*/
            $scope.quoteprice[item_id + '-' + person_id] = retailprice * $scope.costrate[person_id]/ 100;
            // console.log($scope.quoteprice[item_id + '-' + person_id]);
        }
        angular.element(document).ready(function () {
            $('.date').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('.select').select2();
        });
    }

app.controller('priceMatrixController', priceMatrixController);

