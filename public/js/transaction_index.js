var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize']);

    function transController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;  

        $scope.exportData = function () {
            var blob = new Blob([document.getElementById('exportable').innerHTML], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "TransactionRpt"+ now + ".xls");
        };

        var now = moment();
        $scope.today = now.format("DD MMM YY");

        angular.element(document).ready(function () {

            $http.get('/transaction/data').success(function(transactions){
                $scope.transactions = transactions;
                $scope.All = transactions.length;

                $scope.optionStatus = [
                    {name: 'All', value: ''}, 
                    {name: 'Pending', value: 'Pending'},
                    {name: 'Confirmed', value: 'Confirmed'}
                ]; 

                $scope.onDelFromChanged = function(){

                    console.log($scope.deldate_from);
                }
/*                $scope.dateRangeFilter = function (property) {

                    return function (transaction) {
                        if (transaction[property] === null) return false;

                        // console.log(transaction[property]);

                        var deldate = new Date(transaction[property]);

                        $scope.onDelFromChanged = function (startDate){

                            var startdate = new Date(startDate);
                            var enddate = new Date($scope.delTo);

                            if(deldate >= startdate && deldate <= enddate){

                                return true;
                            }

                            return false;
                        }

                        $scope.onDelToChanged = function (endDate){

                            var enddate = new Date(endDate);
                            var startdate = new Date($scope.delFrom);

                            if(deldate >= startdate && deldate <= enddate){

                                return true;
                            }

                            return false;                            

                        }                        
                        
                    }
                } */                      
            });
                
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
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    
 

app.controller('transController', transController);
app.controller('repeatController', repeatController);
