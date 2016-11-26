var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ngSanitize', 'ui.bootstrap.datetimepicker']);

    function customerController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 50;
        $scope.currentPage1 = 1;
        $scope.itemsPerPage1 = 100;
        $scope.indexData = {};
        $scope.total = 0;

        var now = moment();
        $scope.weekstart = now.format("YYYY-MM-DD");
        $scope.weekend = now.format("YYYY-MM-DD");
        getIndex();

        // remain y m d format
        $scope.dateChange = function(date){
            $scope.del_from = moment(date).format("YYYY-MM-DD");
        }

        $scope.dateChange2 = function(date){
            $scope.del_to = moment(date).format("YYYY-MM-DD");
        }

        // upon the search button has been pressed
        $scope.searchDB = function(){
            syncData();
            getIndex();
        }

        // load transaction index data
        function getIndex(){
            $http.post('/market/dtdtrans/index', $scope.indexData).success(function(data){
                $scope.transactions = data.transactions;
                $scope.All = data.transactions.length;
                $scope.total = data.total;
            });
        }

        // sync with search field
        function syncData(){
            $scope.indexData = {
                transaction_id: $scope.id,
                cust_id: $scope.cust_id,
                company: $scope.company,
                status: $scope.status,
                del_from: $scope.del_from,
                del_to: $scope.del_to,
                parent_name: $scope.parent_name,
                type: $scope.type,
            }
        }

        angular.element(document).ready(function () {
            $http.get('/market/customer/data').success(function(customers){
                $scope.customers = customers;
                $scope.All = customers.length;
            });
            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete the entry');
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/person/data/' + id
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

            $scope.exportData = function () {
                var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                    type: "application/vnd.ms-excel;charset=charset=utf-8"
                });
                var now = Date.now();
                saveAs(blob, "CustomerRpt"+ now + ".xls");
            };
        });
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

function repeatController1($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage1 - 1) * $scope.itemsPerPage1;
    })
}

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

$(function() {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('lastTab', $(this).attr('href'));
    });
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
});

app.controller('customerController', customerController);
app.controller('repeatController', repeatController);
app.controller('repeatController1', repeatController1);
