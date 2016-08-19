var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker']);

    function transController($scope, $http){

        // init the variables
        $scope.alldata = [];

        $scope.datasetTemp = {};

        $scope.totalCountTemp = {};

        $scope.totalCount = 0;

        $scope.totalPages = 0;

        $scope.currentPage = 1;

        $scope.itemsPerPage = 70;

        $scope.indexFrom = 0;

        $scope.indexTo = 0;

        $scope.sortBy = true;

        $scope.sortName = '';

        $scope.headerTemp = '';

        $scope.today = moment().format("YYYY-MM-DD");

        $scope.delivery_date = '';

        $scope.updated_at = '';

        // init page load
        getPage(1, true);

        $scope.exportData = function () {

            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {

                type: "application/vnd.ms-excel;charset=charset=utf-8"

            });

            var now = Date.now();

            saveAs(blob, "TransactionRpt"+ now + ".xls");

        };

        // switching page
        $scope.pageChanged = function(newPage){

            getPage(newPage, false);

        };

        $scope.pageNumChanged = function(){

            if($.isEmptyObject($scope.datasetTemp)){

                $scope.datasetTemp = {

                    pageNum: $scope.itemsPerPage

                }

            }else{

                $scope.datasetTemp['pageNum'] = $scope.itemsPerPage;

            }

            getPage(1, false);

        };

        $scope.sortedOrder = function(header){

            $scope.sortName = header;

            if($scope.headerTemp != $scope.sortName){

                $scope.sortBy = true;

                $scope.headerTemp = $scope.sortName;

            }else{

                $scope.sortBy = !$scope.sortBy;

            }

            $scope.datasetTemp['sortName'] = $scope.sortName;

            $scope.datasetTemp['sortBy'] = $scope.sortBy;

            getPage($scope.currentPage, false);

        }

          // when hitting search button
        $scope.searchDB = function(){

            $scope.datasetTemp = {

                id: $scope.search.id,

                cust_id: $scope.search.cust_id,

                company: $scope.search.company,

                status: $scope.search.status,

                pay_status: $scope.search.pay_status,

                updated_by: $scope.search.updated_by,

                updated_at: $scope.search.updated_at,

                delivery_date: $scope.search.delivery_date,

                driver: $scope.search.driver,

                profile: $scope.search.name,

            };

            $scope.sortName = '';

            $scope.sortBy = '';

            if($scope.search.id || $scope.search.cust_id || $scope.search.company || $scope.search.status || $scope.search.pay_status || $scope.search.updated_by || $scope.search.updated_at || $scope.search.delivery_date || $scope.search.driver || $scope.search.name){

                if($.isEmptyObject($scope.datasetTemp)){

                    $scope.datasetTemp = $scope.alldata;

                    $scope.totalCountTemp = $scope.totalCount;

                    $scope.alldata = {};

                }

                getPage(1, false);

            }else{

                if(! $.isEmptyObject($scope.datasetTemp)){

                    $scope.alldata = $scope.datasetTemp;

                    $scope.totalCount = $scope.totalCountTemp;

                    $scope.datasetTemp = {

                        pageNum: $scope.itemsPerPage,

                        sortName: $scope.sortName,

                        sortBy: $scope.sortBy,
                    };

                }

                getPage(1, false);

            }

        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){

            $http.post('transaction/data?page=' + pageNumber + '&init=' + first, $scope.datasetTemp).success(function(data){

                // populate data to ngrepeat
                $scope.alldata = data.transactions.data;

                // count for pagination
                $scope.totalCount = data.transactions.total;

                // get current page
                $scope.currentPage = data.transactions.current_page;

                // get index from
                $scope.indexFrom = data.transactions.from;

                // get index from
                $scope.indexTo = data.transactions.to;

                // get total count
                $scope.All = data.transactions.length;

                // return total amount
                $scope.total_amount = data.total_amount;

            }).error(function(data){

                console.log(data);

            });

        }

        $scope.dateChange = function(date){

            if($('#delivery_date').val() == '' || $('#delivery_date').val() == null){

                // $('#delivery_date').datetimepicker({clear: true});
                $('#delivery_date').val('');

                $scope.search.delivery_date = '';

                // console.log($scope.search.delivery_date);

            }else{

                $scope.search.delivery_date = moment(date).format("YYYY-MM-DD");

            }

            $scope.searchDB();

        }

        $scope.dateChange2 = function(date){

            if($('#updated_at').val() == '' || $('#updated_at').val() == null){

                $('#updated_at').val('');

                $scope.search.updated_at = '';

            }else{

                $scope.search.updated_at = moment(date).format("YYYY-MM-DD");

            }

            $scope.searchDB();

        }

        //delete record
        $scope.confirmDelete = function(id){

            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);

            if(isConfirmDelete){

                $http({

                    method: 'DELETE',

                    url: '/transaction/data/' + id

                }).success(function(data){

                    location.reload();

                }).error(function(data){

                    alert('Unable to delete');

                })

            }else{

                return false;

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