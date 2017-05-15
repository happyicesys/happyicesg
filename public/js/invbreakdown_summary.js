var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ui.bootstrap.datetimepicker',
                                    'datePicker'
                                ]);

    function invbreakdownSummaryController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 200;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
        $scope.search = {
            profile_id: '',
            delivery_from: moment().startOf('month').format('YYYY-MM-DD'),
            delivery_to: moment().format("YYYY-MM-DD"),
            status: 'Delivered',
            cust_id: '',
            company: '',
            person_id: '',
            custcategory: '',
            pageNum: 200,
            sortBy: true,
            sortName: ''
        }
        $scope.updated_at = '';
        // init page load
        getPage(1, true);

        angular.element(document).ready(function () {
            $('.select').select2();
        });

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable_invbreakdownsummary').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Invoice Breakdown Summary"+ now + ".xls");
        };

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

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

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/invbreakdown/summary?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.deals.data){
                    $scope.alldata = data.deals.data;
                    $scope.totalCount = data.deals.total;
                    $scope.currentPage = data.deals.current_page;
                    $scope.indexFrom = data.deals.from;
                    $scope.indexTo = data.deals.to;
                }else{
                    $scope.alldata = data.deals;
                    $scope.totalCount = data.deals.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.deals.length;
                }
                // get total count
                $scope.All = data.deals.length;

                // return total amount
                $scope.total_revenue = data.total_revenue;
                $scope.total_gross_money = data.total_gross_money;
                $scope.total_gross_percent = data.total_gross_percent;
                $scope.spinner = false;
            });
        }
    }



app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            if(input) {
                return $filter('date')(new Date(input), format);
            }else {
                return '';
            }
        };
    }
]);

app.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

app.config(function ($provide){
    $provide.decorator('mFormatFilter', function (){
        return function newFilter(m, format, tz)
        {
            if (!(moment.isMoment(m))) {
                return '';
            }
            return tz ? moment.tz(m, tz).format(format) : m.format(format);
        };
    });
});

app.controller('invbreakdownSummaryController', invbreakdownSummaryController);