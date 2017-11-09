var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

    function operationWorksheetController($scope, $http){

        $('#checkAll').change(function(){
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });

        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 'All';
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            profile_id: '',
            id_prefix: '',
            custcategory: '',
            cust_id: '',
            company: '',
            chosen_date: moment().format('YYYY-MM-DD'),
            previous: 'Last 7 days',
            future: '2 days',
            color: '',
            pageNum: 'All',
            sortBy: true,
            sortName: ''
        }
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();

            $('.date').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        });

        $scope.exportData = function ($event) {
            $event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Operation Worksheet"+ now + ".xls");
        };

        $scope.onChosenDateChanged = function(date){
            if(date){
                $scope.search.chosen_date = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

          // when hitting search button
        $scope.searchDB = function(){
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
        }

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1, false);
        }

        $scope.changeColor = function(alldata, parent_index, index) {
            if(!alldata.qty) {
                $http.post('/api/detailrpt/operation/color', {'id': alldata.id}).success(function(data) {
                    $scope.alldata[parent_index][index]['color'] = data.color;
                });
            }
        }

        $scope.todayDateChecker = function(date) {
            if(date === $scope.search.chosen_date) {
                return 'Lightpurple';
            }
        }

        $scope.updateOpsNotes = function(person_id, operation_note) {
            $http.post('/api/detailrpt/operation/note/' + person_id, {'operation_note': operation_note}).success(function(date) {
            });
        }

        $scope.getBackgroundColor = function(alldata, parent_index, index) {

            if(alldata.bool_transaction) {
                if(!alldata.qty) {
                    $scope.alldata[parent_index][index]['qty'] = 0;
                }
                return '#77d867';
            }else {
                if(alldata.color) {
                    return alldata.color;
                }else {
                    return '';
                }
            }
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/operation?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.people.data){
                    $scope.dates = data.dates;
                    $scope.alldata = data.alldata;
                    $scope.people = data.people.data;
                    $scope.totalCount = data.people.total;
                    $scope.currentPage = data.people.current_page;
                    $scope.indexFrom = data.people.from;
                    $scope.indexTo = data.people.to;
                }else{
                    $scope.dates = data.dates;
                    $scope.people = data.people;
                    $scope.alldata = data.alldata;
                    $scope.totalCount = data.people.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.people.length;
                }
                // get total count
                $scope.All = data.people.length;
                // return fixed total amount
                $scope.spinner = false;
            });
        }
    }

app.controller('operationWorksheetController', operationWorksheetController);