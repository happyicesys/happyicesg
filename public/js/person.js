var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

function personController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.datasetTemp = {};
    $scope.totalCountTemp = {};
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.headerTemp = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        cust_id: '',
        custcategory: '',
        company: '',
        contact: '',
        active: ['Yes'],
        pageNum: 100,
        profile_id: '',
        franchisee_id: ''
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
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
        $http.post('/api/people?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
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

app.controller('personController', personController);
