var app = angular.module('app', [
                            'ui.bootstrap',
                            'angularUtils.directives.dirPagination',
                            'ae-datetimepicker'
                            ]);

function analogDifferenceController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        cust_id: '',
        company: '',
        pageNum: 100,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Rpt"+ now + ".xls");
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

      // when hitting search button
    $scope.searchDB = function(){
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve franchisee id
    $scope.getFranchiseeId = function() {
        $http.get('/api/franchisee/auth').success(function(data) {
            return data;
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/franchisee/people?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.people.data){
                $scope.alldata = data.people.data;
                $scope.totalCount = data.people.total;
                $scope.currentPage = data.people.current_page;
                $scope.indexFrom = data.people.from;
                $scope.indexTo = data.people.to;
            }else{
                $scope.alldata = data.people;
                $scope.totalCount = data.people.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.people.length;
            }
            // get total count
            $scope.All = data.people.length;

            // return total amount
            $scope.spinner = false;
        });
    }
}

function varianceManagementController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.form = {

    };
    $scope.search = {
        datein_from: $scope.today,
        datein_to: $scope.today,
        cust_id: '',
        company: '',
        pageNum: 100,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Rpt"+ now + ".xls");
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

      // when hitting search button
    $scope.searchDB = function(){
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve franchisee id
    $scope.getFranchiseeId = function() {
        $http.get('/api/franchisee/auth').success(function(data) {
            return data;
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/franchisee/people?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.people.data){
                $scope.alldata = data.people.data;
                $scope.totalCount = data.people.total;
                $scope.currentPage = data.people.current_page;
                $scope.indexFrom = data.people.from;
                $scope.indexTo = data.people.to;
            }else{
                $scope.alldata = data.people;
                $scope.totalCount = data.people.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.people.length;
            }
            // get total count
            $scope.All = data.people.length;

            // return total amount
            $scope.spinner = false;
        });
    }
}
$(function() {
    // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // save the latest tab; use cookies if you like 'em better:
        localStorage.setItem('lastTab', $(this).attr('href'));
    });

    // go to the latest tab, if it exists:
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
});

app.controller('analogDifferenceController', analogDifferenceController);
app.controller('varianceManagementController', varianceManagementController);
