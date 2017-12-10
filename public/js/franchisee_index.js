var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ae-datetimepicker'
                                ]);

    function fTransactionController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            id: '',
            cust_id: '',
            company: '',
            status: '',
            pay_status: '',
            updated_by: '',
            updated_at: '',
            collection_from: $scope.today,
            collection_to: $scope.today,
            itemsPerPage: 100,
            sortName: '',
            sortBy: true
        }
        $scope.form = {
            person_id: '',
            digital_clock: '',
            analog_clock: '',
            collection_date: '',
            collection_time: '',
            total: ''
        }
        $scope.formErrors = [];
        // init page load
        getPage(1);

        angular.element(document).ready(function () {
            $('.select').select2({
                placeholder: 'Select..'
            });
            $('.selectall').select2();
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "FVendCash"+ now + ".xls");
        };

        $scope.collectionFromChanged = function(date){
            if(date){
                $scope.search.collection_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.collectionToChanged = function(date){
            if(date){
                $scope.search.collection_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.dateChange2 = function(date){
            if(date){
                $scope.search.updated_at = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.onPrevDateClicked = function() {
            $scope.search.collection_from = moment(new Date($scope.search.collection_from)).subtract(1, 'days').format('YYYY-MM-DD');
            $scope.search.collection_to = moment(new Date($scope.search.collection_to)).subtract(1, 'days').format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onTodayDateClicked = function() {
            $scope.search.collection_from = moment().format('YYYY-MM-DD');
            $scope.search.collection_to = moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextDateClicked = function() {
            $scope.search.collection_from = moment(new Date($scope.search.collection_from)).add(1, 'days').format('YYYY-MM-DD');
            $scope.search.collection_to = moment(new Date($scope.search.collection_to)).add(1, 'days').format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
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
            $scope.formErrors = [];
            getPage(1);
        }

        $scope.addEntry = function() {
            let inputData = {
                'person_id': $scope.form.person_id,
                'digital_clock': $scope.form.digital_clock,
                'analog_clock': $scope.form.analog_clock,
                'collection_date': $scope.form.collection_date ? moment(new Date($scope.form.collection_date)).format('YYYY-MM-DD') : null,
                'collection_time': $scope.form.collection_time ?  moment(new Date($scope.form.collection_time)).format('HH:mm') : null,
                'total': $scope.form.total,
                'franchisee_id': $scope.search.franchisee_id ? $scope.search.franchisee_id : null
            }
            $http.post('/api/franchisee/submitEntry', inputData). success(function(data) {
                getPage(1);

                $scope.form = {
                    digital_clock: '',
                    analog_clock: '',
                    collection_date: '',
                    collection_time: '',
                    total: ''
                }

                $('.select').val('').trigger('change')
            }).error(function(data, status) {
                $scope.formErrors = data;
            });
        }

        $scope.removeEntry = function(id) {
            console.log(id);
            $http.delete('/api/franchisee/entry/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }

        $scope.isFormValid = function() {
            let invalid = true;
            if($scope.form.person_id && $scope.form.total) {
                invalid = false;
            }
            return invalid;
        }

        // retrieve page w/wo search
        function getPage(pageNumber){
            $scope.spinner = true;
            $http.post('/api/franchisee?page=' + pageNumber, $scope.search).success(function(data){
                if(data.ftransactions.data){
                    $scope.alldata = data.ftransactions.data;
                    $scope.totalCount = data.ftransactions.total;
                    $scope.currentPage = data.ftransactions.current_page;
                    $scope.indexFrom = data.ftransactions.from;
                    $scope.indexTo = data.ftransactions.to;
                }else{
                    $scope.alldata = data.ftransactions;
                    $scope.totalCount = data.ftransactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.ftransactions.length;
                }
                // get total count
                $scope.All = data.ftransactions.length;

                // return total amount
                $scope.total_vend_amount = data.total_vend_amount;
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
                    url: '/ftransaction/data/' + id
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

app.controller('fTransactionController', fTransactionController);