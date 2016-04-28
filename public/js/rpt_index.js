// rpt_index.js

var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker']);

    function rptController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 70;
        $scope.payMethodModel = 'cash';
        $scope.indexData = {};

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "TransactionRpt"+ now + ".xls");
        };

        var now = moment();
        $scope.today = now.format("YYYY-MM-DD");

        angular.element(document).ready(function () {

            getIndex();
/*
            $http.get('/user/data/' + $('#user_id').val()).success(function(person){

                var driver = false;

                for(var i = 0; i < person.roles.length; i++){

                    if(person.roles[i].name === 'driver'){

                        driver = true;

                    }
                }
                if(driver){

                    $scope.driver_paid = person.name;

                }
            });*/

            function getIndex(){

                $http.post('/report/dailyrpt', $scope.indexData).success(function(transactions){

                    $scope.transactions = transactions;

                    $scope.All = transactions.length;

                });

            }

            $scope.dateChange = function(date){

                $scope.delivery_date = moment(date).format("YYYY-MM-DD");

                $scope.indexData = {

                    delivery_date: $scope.delivery_date,

                    paid_at: $scope.paid_at,

                }

                getIndex();
            }

            $scope.dateChange2 = function(date){

                $scope.paid_at = moment(date).format("YYYY-MM-DD");

                $scope.indexData = {

                    delivery_date: $scope.delivery_date,

                    paid_at: $scope.paid_at,

                }

                getIndex();
            }

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/transaction/data/' + id
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
        });

            $scope.onVerifiedPaid = function($event, transaction_id, payMethodModel, noteModel){

                $http({
                    url: '/transaction/rpt/' + transaction_id ,
                    method: "POST",
                    data: {
                            paymethod: payMethodModel,
                            note: noteModel,
                            },
                    }).success(function(response){
                });
            }
    }

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}


app.controller('rptController', rptController);
app.controller('repeatController', repeatController);
