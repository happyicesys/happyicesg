var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

    function transController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            id: '',
            cust_id: '',
            company: '',
            status: '',
            pay_status: '',
            updated_by: '',
            updated_at: '',
            delivery_from: $scope.today,
            delivery_to: $scope.today,
            driver: '',
            custcategory: '',
            itemsPerPage: 100,
            sortName: '',
            sortBy: true
        }
        $scope.updated_at = '';
        // init page load
        getPage(1);

        angular.element(document).ready(function () {
            $('.select').select2();
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "TransactionRpt"+ now + ".xls");
        };

        $scope.delFromChange = function(date){
            if(date){
                $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.delToChange = function(date){
            if(date){
                $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.dateChange2 = function(date){
            if(date){
                $scope.search.updated_at = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.onTodayDateClicked = function() {
            $scope.search.delivery_from = moment().format('YYYY-MM-DD');
            $scope.search.delivery_to = moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onYesterdayDateClicked = function() {
            $scope.search.delivery_from = moment().subtract(1, 'days').format('YYYY-MM-DD');
            $scope.search.delivery_to = moment().subtract(1, 'days').format('YYYY-MM-DD');
            $scope.searchDB();
        }

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1)
        };

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1);
        }

          // when hitting search button
        $scope.searchDB = function(){
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1);
        }

        // retrieve page w/wo search
        function getPage(pageNumber){
            $scope.spinner = true;
            $http.post('transaction/data?page=' + pageNumber, $scope.search).success(function(data){
                if(data.transactions.data){
                    $scope.alldata = data.transactions.data;
                    $scope.totalCount = data.transactions.total;
                    $scope.currentPage = data.transactions.current_page;
                    $scope.indexFrom = data.transactions.from;
                    $scope.indexTo = data.transactions.to;
                }else{
                    $scope.alldata = data.transactions;
                    $scope.totalCount = data.transactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.transactions.length;
                }
                // get total count
                $scope.All = data.transactions.length;

                // return total amount
                $scope.total_amount = data.total_amount;
                $scope.spinner = false;
            }).error(function(data){

            });
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

app.controller('transController', transController);