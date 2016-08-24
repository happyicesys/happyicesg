var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker']);

    function transController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 70;
        $scope.indexData = {};
        $scope.totalDeal = 0;
        $scope.totalComm = 0;

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "TransactionRpt"+ now + ".xls");
        };

        // get today's date
        var now = moment().isoWeekday(1);
        // find out start day of the week (Monday)
        $scope.weekstart = now.startOf('week').add(1, 'day').format("YYYY-MM-DD");
        // find out end day of the week (Sunday)
        $scope.weekend = now.endOf('week').add(1, 'day').format("YYYY-MM-DD");
        // initialization
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
            $http.post('/market/deal/index', $scope.indexData).success(function(data){
                $scope.transactions = data.transactions;
                $scope.All = data.transactions.length;
                $scope.totalDeal = data.totalDeal;
                $scope.totalComm = data.totalComm;
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

app.controller('transController', transController);
app.controller('repeatController', repeatController);