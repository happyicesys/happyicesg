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
            saveAs(blob, "DailyRpt"+ now + ".xls");
        };

        var now = moment();
        $scope.today = now.format("YYYY-MM-DD");

        angular.element(document).ready(function () {



                $http.get('/user/data/' + $('#user_id').val()).success(function(person){

                    var driver = false;

                    for(var i = 0; i < person.roles.length; i++){

                        if(person.roles[i].name === 'driver'){

                            driver = true;

                            break;
                        }
                    }

                    $scope.getdriver = function(){

                        return driver;

                    }
                });

            // first init
            getIndex();

            function getIndex(){

                $http.post('/report/dailyrpt', $scope.indexData).success(function(transactions){

                    $scope.transactions = transactions;

                    $scope.All = transactions.length;

                });

                $http.post('/report/dailyrec', $scope.indexData).success(function(rptdata){

                    $scope.rptdata = rptdata;

                });

            }

            function syncData(){

                $scope.indexData = {

                    delivery_date: $scope.delivery_date,

                    paid_at: $scope.paid_at,

                    paid_by: $scope.paid_by,

                    driver: $scope.driver,

                }
            }
/*
            function syncDataAll(){

                $scope.indexData = {

                    delivery_date: $scope.delivery_date,

                    paid_at: $scope.paid_at,

                    paid_by: $scope.paid_by,

                    driver: $scope.driver,

                    transaction_id: $scope.search.id,

                    cust_id: $scope.search.cust_id,

                    company: $scope.search.company,

                    status: $scope.search.status,

                    pay_status: $scope.search.pay_status,

                }
            }*/
/*
            $scope.exportPDF = function(){

                // syncDataAll();

                $http.post('/report/dailypdf', $scope.indexData).success(function(){

                    $scope.indexData['transaction_id'] = '';

                    $scope.indexData['cust_id'] = '';

                    $scope.indexData['company'] = '';

                    $scope.indexData['status'] = '';

                    $scope.indexData['pay_status'] = '';

                });
            }*/

            $scope.dateChange = function(date){

                $scope.delivery_date = moment(date).format("YYYY-MM-DD");

                $scope.paid_at = moment(date).format("YYYY-MM-DD");

                syncData();

                getIndex();
            }

            $scope.dateChange2 = function(date){

                $scope.paid_at = moment(date).format("YYYY-MM-DD");

                $scope.delivery_date = moment(date).format("YYYY-MM-DD");

                syncData();

                getIndex();
            }


            $scope.paidByChange = function(paid_by){

                $scope.driver = paid_by;

                syncData();

                getIndex();
            }


            $scope.driverChange = function(driver){

                $scope.paid_by = driver;

                syncData();

                getIndex();
            }
        });

        $scope.onVerifiedPaid = function($event, transaction_id, payMethodModel, noteModel){
            $event.preventDefault();
            $http({
                url: '/transaction/rpt/' + transaction_id ,
                method: "POST",
                data: {
                        paymethod: payMethodModel,
                        note: noteModel,
                        },
                }).success(function(response){

                    $http.get('/transaction/status/'+ transaction_id).success(function(){
                        location.reload();
                    });
            });
        }

        $scope.exportAction = function(){

            switch($scope.export_action){

                case 'pdf': $scope.$broadcast('export-pdf', {});
                          break;
                case 'excel': $scope.$broadcast('export-excel', {});
                          break;
                case 'doc': $scope.$broadcast('export-doc', {});
                          break;
                default: console.log('no event caught');
            }
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

(function(){
//export html table to pdf, excel and doc format directive
var exportTable = function(){

    var link = function($scope, elm, attr){

        $scope.$on('export-pdf', function(e, d){
            elm.tableExport({type:'pdf', escape:false});
        });

        $scope.$on('export-excel', function(e, d){
            elm.tableExport({type:'excel', escape:false});
        });

        $scope.$on('export-doc', function(e, d){
            elm.tableExport({type: 'doc', escape:false});
        });
    }

return {
    restrict: 'C',
    link: link
   }
}

angular
    .module('CustomDirectives', [])
    .directive('exportTable', exportTable);
})();


app.controller('rptController', rptController);
app.controller('repeatController', repeatController);
