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
        var isConfirmDelete = confirm('Are you sure to DELETE this category, its components and parts?');
        if(isConfirmDelete){
            $http.delete('/api/bom/category/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
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
        component_name: '',
        category_id: '',
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
    $scope.formcomponents = [
        {
            name: '',
            remark: ''
        },
    ];

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select..'
        });
    });

    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomcomponent').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BoM_Components_By_CAT_"+ now + ".xls");
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
        $http.post('/api/bom/category/components?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.components.data){
                $scope.alldata = data.components.data;
                $scope.totalCount = data.components.total;
                $scope.currentPage = data.components.current_page;
                $scope.indexFrom = data.components.from;
                $scope.indexTo = data.components.to;
            }else{
                $scope.alldata = data.components;
                $scope.totalCount = data.components.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.components.length;
            }
            $scope.All = data.components.length;
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        });
    }

    $scope.removeEntry = function(id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this component and its parts?');
        if(isConfirmDelete){
            $http.delete('/api/bom/category/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.category_id) {
            invalid = false;
        }
        return invalid;
    }

    $scope.addRow = function() {
        $scope.formcomponents.push({
            name: '',
            remark: ''
        })
    }

    $scope.confirmComponents = function(category_id) {
        let inputData = {
            'components': $scope.formcomponents
        }
        $http.post('/api/bom/component/batchcreate/' + category_id , inputData).success(function(data) {
            $scope.formcomponents = [
                {
                    name: '',
                    remark: ''
                },
            ];
            getPage(1);
        })
    }
}

function bomPartController($scope, $http){
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
        category_name: '',
        component_id: '',
        component_name: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        category_id: '',
        component_id: '',
    }
    $scope.formparts = [
        {
            name: '',
            remark: ''
        },
    ];
    $scope.componentSelectList = [];

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select..'
        });
        $('.selectcom').select2({
            placeholder: 'Select..'
        });
    });

    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomcomponent').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BoM_Parts"+ now + ".xls");
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

    $scope.getComponentSelectList = function(category_id) {
        $http.get('/api/bom/components/category/' + category_id).success(function(data) {
            $scope.componentSelectList = data;
            $('.selectcom').val(null).trigger('change.select2');
        });
    }

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
        $http.post('/api/bom/parts?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.bomparts.data){
                $scope.alldata = data.bomparts.data;
                $scope.totalCount = data.bomparts.total;
                $scope.currentPage = data.bomparts.current_page;
                $scope.indexFrom = data.bomparts.from;
                $scope.indexTo = data.bomparts.to;
            }else{
                $scope.alldata = data.bomparts;
                $scope.totalCount = data.bomparts.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bomparts.length;
            }
            $scope.All = data.bomparts.length;
            $scope.total_amount = data.total_amount;
            $scope.spinner = false;
        });
    }

    $scope.removeEntry = function(id) {
        var isConfirmDelete = confirm('Are you sure to this parts?');
        if(isConfirmDelete){
            $http.delete('/api/bom/part/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.component_id) {
            invalid = false;
        }
        return invalid;
    }

    $scope.addRow = function() {
        $scope.formparts.push({
            name: '',
            remark: ''
        })
    }

    $scope.confirmParts = function(component_id) {
        let inputData = {
            'parts': $scope.formparts
        }
        $http.post('/api/bom/parts/batchcreate/' + component_id , inputData).success(function(data) {
            $scope.formparts = [
                {
                    name: '',
                    remark: '',
                },
            ];
            getPage(1);
        })
    }

}


app.controller('bomCategoryController', bomCategoryController);
app.controller('bomComponentController', bomComponentController);
app.controller('bomPartController', bomPartController);

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
