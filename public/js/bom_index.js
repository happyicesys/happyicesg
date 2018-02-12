var app = angular.module('app', [
                                    'ngSanitize',
                                    'ui.select2',
                                    'angularUtils.directives.dirPagination',
                                    '720kb.datepicker',
                                ]);

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

    $scope.select2Options = {
        'multiple': true,
        'simple_tags': true,
        'tags': []  // Can be empty list.
    };

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

function bomTemplateController($scope, $timeout, $http){
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
        custcategory_id: '',
        custcategory_name: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        part_id: '',
    }
    $scope.componentSelectList = [];
    $scope.spinner = false;
    $scope.is_done = false;

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select..'
        });
        $('.selectpart').select2({
            placeholder: 'Select..',
            allowClear: true
        });
    });

    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomtemplate').innerHTML], {
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
        $http.post('/api/bom/templates?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.bomtemplates.data){
                $scope.alldata = data.bomtemplates.data;
                $scope.totalCount = data.bomtemplates.total;
                $scope.currentPage = data.bomtemplates.current_page;
                $scope.indexFrom = data.bomtemplates.from;
                $scope.indexTo = data.bomtemplates.to;
            }else{
                $scope.alldata = data.bomtemplates;
                $scope.totalCount = data.bomtemplates.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bomtemplates.length;
            }
            $scope.All = data.bomparts.length;
            $scope.total_amount = data.total_amount;
        });
    }

    $scope.removeEntry = function(id) {
        var isConfirmDelete = confirm('Are you sure to this binding?');
        if(isConfirmDelete){
            $http.delete('/api/bom/template/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.part_id) {
            invalid = false;
        }
        return invalid;
    }

    $scope.confirmTemplate = function(custcategory_id) {
        $http.post('/api/bom/template/custcategory/' + custcategory_id , {'bompart_id': $scope.form.part_id}).success(function(data) {
            $('.selectpart').val(null).trigger('change.select2');
            getPage(1);
        })
    }

    $scope.onCustcategoryChanged = function() {
        $('.selectpart').val(null).trigger('change.select2');
        $http.get('/api/custcat/' + $scope.search.custcategory_id).success(function(data) {
            $scope.search.custcategory_name = data.name;
        });
        getPage(1, false);
    }

    $scope.overwriteBom = function($event, custcategory_id){
        $event.preventDefault();
        var isConfirmOverwrite = confirm('Are you sure you want to overwrite all BOM?');
        if(isConfirmOverwrite){
            $scope.spinner = true;
            $http.post('/api/bom/synctemplate/bomvending', {'custcategory_id': custcategory_id}).success(function(data) {
                getPage(1, false);
                $scope.spinner = false;
                $scope.is_done = true;
                $timeout(function() {
                 $scope.is_done = false;
                }, 5000);
            });
        }else{
            return false;
        }
    }
}

function bomVendingController($scope, $http){
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
        person_id: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.formsearches = [
        {
            person_id: ''
        }
    ];
    $scope.componentSelectList = [];
    $scope.selectpeople = [];

    angular.element(document).ready(function () {
        $('.select-0').select2({
            placeholder: 'Select..'
        });
        $('.selectpart').select2({
            placeholder: 'Select..',
            allowClear: true
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomvending').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BoM_Comparison"+ now + ".xls");
    };

    function getBomList() {
        $http.post('/api/bom/vendings', {formsearches: $scope.formsearches}).success(function(data) {
            $scope.people = data.people;
            $scope.bomcategories = data.bomcategories;
            $scope.bomcomponents = data.bomcomponents;
            $scope.bomvendings = data.bomvendings;
        });
    }

    $scope.generateBomList = function() {
        getBomList();
    }

    $scope.onCustcategoryChanged = function() {
        $('.selectpart').val(null).trigger('change.select2');
        getPage(1, false);
    }

    $scope.addCustomer = function(formsearches) {
        $scope.formsearches.push({
            person_id: ''
        });

        var selectclass = '';
        selectclass = '.select-' + formsearches.length;
        console.log(selectclass);
        $(selectclass).select2({
            placeholder: 'Select..'
        });
    }

    $scope.getTemplateOptions = function(component_id, custcategory_id) {
        $http.get('/api/template/component/' + component_id + '/custcategory/' + custcategory_id).success(function(data) {
            return data;
        });
    }

    $scope.onOtherPartChoosen = function(vending_id, part_id)
    {
        $http.post('/api/bomvending/part/change', {'vending_id': vending_id, 'part_id': part_id}).success(function(data) {
            getBomList();
        });
    }
}

function maintenanceController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        person_id: '',
        date_from: $scope.today,
        date_to: $scope.today,
        custcategory_id: '',
        technician_id: '',
        bomcomponent_id: '',
        issue_type: '',
        itemsPerPage: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        person_id: '',
        date: '',
        time: '',
        technician_id: '',
        urgency: '',
        time_spend: '',
        bomcomponent_id: '',
        issue_type: '',
        solution: '',
        remark: ''
    }
    $scope.formErrors = [];
    // init page load
    getPage(1);

    angular.element(document).ready(function () {
        $('.selectform').select2({
            placeholder: 'Select..'
        });
        $('.selectsearch').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_maintenance').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BOM Maintenance"+ now + ".xls");
    };

    $scope.dateFromChanged = function(date){
        if(date){
            $scope.search.date_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.dateToChanged = function(date){
        if(date){
            $scope.search.date_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onPrevDateClicked = function() {
        $scope.search.date_from = moment(new Date($scope.search.date_from)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.search.date_to = moment(new Date($scope.search.date_to)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onTodayDateClicked = function() {
        $scope.search.date_from = moment().format('YYYY-MM-DD');
        $scope.search.date_to = moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextDateClicked = function() {
        $scope.search.date_from = moment(new Date($scope.search.date_from)).add(1, 'days').format('YYYY-MM-DD');
        $scope.search.date_to = moment(new Date($scope.search.date_to)).add(1, 'days').format('YYYY-MM-DD');
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
            'date': $scope.form.date ? moment(new Date($scope.form.date)).format('YYYY-MM-DD') : null,
            'time': $scope.form.time ?  moment(new Date($scope.form.time)).format('HH:mm') : null,
            'technician_id': $scope.form.technician_id,
            'urgency': $scope.form.urgency,
            'time_spend': $scope.form.time_spend,
            'bomcomponent_id': $scope.form.bomcomponent_id,
            'issue_type': $scope.form.issue_type,
            'solution': $scope.form.solution,
            'remark': $scope.form.remark
        }

        $http.post('/api/bom/maintenance/submit', inputData). success(function(data) {
            getPage(1);

            $scope.form = {
                person_id: '',
                date: '',
                time: '',
                technician_id: '',
                urgency: '',
                time_spend: '',
                bomcomponent_id: '',
                issue_type: '',
                solution: '',
                remark: ''
            }

            $('.selectform').val(null).trigger('change.select2');
        }).error(function(data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function(id) {
        $http.delete('/api/bom/maintenance/' + id + '/delete').success(function(data) {
            getPage(1);
        });
    }

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.person_id && $scope.form.technician_id && $scope.form.bomcomponent_id && $scope.form.issue_type && $scope.form.solution) {
            invalid = false;
        }
        return invalid;
    }
/*
    $scope.changeRemarks = function(id, remarks) {
        $http.post('/api/franchisee/edit/' + id, {'remarks': remarks}).success(function(data) {
        });
    }*/

    // retrieve page w/wo search
    function getPage(pageNumber){
        $scope.spinner = true;
        $http.post('/api/bom/maintenances?page=' + pageNumber, $scope.search).success(function(data){
            if(data.bommaintenances.data){
                $scope.alldata = data.bommaintenances.data;
                $scope.totalCount = data.bommaintenances.total;
                $scope.currentPage = data.bommaintenances.current_page;
                $scope.indexFrom = data.bommaintenances.from;
                $scope.indexTo = data.bommaintenances.to;
            }else{
                $scope.alldata = data.bommaintenances;
                $scope.totalCount = data.bommaintenances.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bommaintenances.length;
            }
            // get total count
            $scope.All = data.bommaintenances.length;

            $scope.spinner = false;
        }).error(function(data){

        });
    }
}



app.controller('bomCategoryController', bomCategoryController);
app.controller('bomComponentController', bomComponentController);
app.controller('bomPartController', bomPartController);
app.controller('bomTemplateController', bomTemplateController);
app.controller('bomVendingController', bomVendingController);
app.controller('maintenanceController', maintenanceController);

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
