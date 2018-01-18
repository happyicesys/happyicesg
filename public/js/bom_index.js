var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.bootstrap.datetimepicker']);

function bomCategoryController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        category_id: '',
        name: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        name: '',
        remark: ''
    }
    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomcategory').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BoM_Categories_"+ now + ".xls");
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

    $scope.sortTable = function(sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = ! $scope.search.sortBy;
        getPage(1, false);
    }

      // when hitting search button
    $scope.searchDB = function(){
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/bom/categories?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.bomcategories.data){
                $scope.alldata = data.bomcategories.data;
                $scope.totalCount = data.bomcategories.total;
                $scope.currentPage = data.bomcategories.current_page;
                $scope.indexFrom = data.bomcategories.from;
                $scope.indexTo = data.bomcategories.to;
            }else{
                $scope.alldata = data.bomcategories;
                $scope.totalCount = data.bomcategories.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bomcategories.length;
            }
            $scope.All = data.bomcategories.length;
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        });
    }

    $scope.addEntry = function() {
        let inputData = {
            'name': $scope.form.name,
            'remark': $scope.form.remark
        }
        $http.post('/api/bom/category/create', inputData). success(function(data) {
            getPage(1);

            $scope.form = {
                name: '',
                remark: ''
            }
        }).error(function(data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function(id) {
        $http.delete('/api/bom/category/' + id + '/delete').success(function(data) {
            getPage(1);
        });
    }

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.name) {
            invalid = false;
        }
        return invalid;
    }
}

function bomComponentController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        component_id: '',
        name: '',
        category_name: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        category_id: '',
        name: '',
        remark: ''
    }
    $scope.formcomponents = {
        index: 1,
        name: ''
    }

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select..'
        });
    });

    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomcategory').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BoM_Categories_"+ now + ".xls");
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

    $scope.sortTable = function(sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = ! $scope.search.sortBy;
        getPage(1, false);
    }

      // when hitting search button
    $scope.searchDB = function(){
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/bom/categories?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.bomcategories.data){
                $scope.alldata = data.bomcategories.data;
                $scope.totalCount = data.bomcategories.total;
                $scope.currentPage = data.bomcategories.current_page;
                $scope.indexFrom = data.bomcategories.from;
                $scope.indexTo = data.bomcategories.to;
            }else{
                $scope.alldata = data.bomcategories;
                $scope.totalCount = data.bomcategories.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bomcategories.length;
            }
            $scope.All = data.bomcategories.length;
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        });
    }

    $scope.addEntry = function() {
        let inputData = {
            'name': $scope.form.name,
            'remark': $scope.form.remark
        }
        $http.post('/api/bom/category/create', inputData). success(function(data) {
            getPage(1);

            $scope.form = {
                name: '',
                remark: ''
            }
        }).error(function(data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function(id) {
        $http.delete('/api/bom/category/' + id + '/delete').success(function(data) {
            getPage(1);
        });
    }

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.name) {
            invalid = false;
        }
        return invalid;
    }
}


app.controller('bomCategoryController', bomCategoryController);
app.controller('bomComponentController', bomComponentController);

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
