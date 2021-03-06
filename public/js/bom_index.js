var app = angular.module('app', [
        'ngSanitize',
        'ui.select2',
        'ui.select',
        'angularUtils.directives.dirPagination',
        '720kb.datepicker',
    ], ['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.post['X-CSRF-TOKEN'] = $('meta[name=csrf-token]').attr('content');
}]);

function bomCategoryController($scope, $http, $window){
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
    $scope.categoryform = {};
    // init page load
    getPage(1, true);
    getCustcatOptions();
    getCustcatOptions();

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

    function getCustcatOptions() {
        $http.get('/custcat/data').success(function(data) {
            $scope.custcategories = data;
        });
    }
/*
    function getBomcomponentCustcatOptions(bomcomponent_id) {
        $http.get('/api/custcat/bomcomponent/' + bomcomponent_id).success(function(data) {
            $scope.bomcomponentcustcats = data;
        });
    }*/

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

    $scope.editCategoryModal = function(bomcategory) {
        fetchSingleBomcategory(bomcategory);
    }

    function fetchSingleBomcategory(bomcategory) {
        $scope.categoryform = {
            id: bomcategory.id,
            category_id: bomcategory.category_id,
            name: bomcategory.name,
            remark: bomcategory.remark,
            drawing_id: bomcategory.drawing_id,
            drawing_path: bomcategory.drawing_path
        }
    }

    $scope.editCategory = function() {
        $http.post('/api/bomcategory/single/update', $scope.categoryform).success(function(data) {
            getPage(1);
        });
    }

    $scope.errors = [];
    $scope.files = [];
    var formData = new FormData();

    $scope.uploadFile = function (bomcategory_id) {
        var request = {
            method: 'POST',
            url: '/bomcategory/drawing/upload/' + bomcategory_id,
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#image_file');
                fileElement.value = '';
                alert("Image has been uploaded successfully!");
                $http.get('/api/bomcategory/' + bomcategory_id).success(function(data) {
                    fetchSingleBomcategory(data);
                });
            }, function error(e) {
                $scope.errors = e.data.errors;
            });
    };

    $scope.setTheFiles = function ($files) {
        angular.forEach($files, function (value, key) {
            formData.append('image_file', value);
        });
    };

    $scope.deleteBomcategoryDrawing = function(bomcategory_id) {
        $http.delete('/api/bomcategory/drawing/'+ bomcategory_id + '/delete').success(function(data) {
            $http.get('/api/bomcategory/' + bomcategory_id).success(function(catdata) {
                fetchSingleBomcategory(catdata);
            });
        });
    }
/*
    $scope.onBomcategoryCustcatChosen = function(item, bomcategory_id) {
        $http.post('/api/bomcategory/' + bomcategory_id + '/custcategories/add', {custcategory: item}).success(function(data) {
            getPage(1);
        });
    }

    $scope.onBomcategoryCustcatRemoval = function(item, bomcategory_id) {
        $http.post('/api/bomcategory/' + bomcategory_id + '/custcategories/remove', {custcategory: item}).success(function(data) {
            getPage(1);
        });
    }*/

    $scope.onBomcategoryCustcatChosen = function(bomcategory_id, custcategory_id) {
        $http.post('/api/bomcategory/custcat', {bomcategory_id: bomcategory_id, custcategory_id: custcategory_id}).success(function(data) {
            getPage(1);
        });
    }
}

function bomComponentController($scope, $timeout, $http){
    // init the variables
    $scope.alldata = [];
    $scope.to_custcategories = [];
    $scope.partform = {};
    $scope.componentform = {};
    $scope.conpartform = {};
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.spinner = false;
    $scope.is_done = false;
    $scope.search = {
        component_id: '',
        component_name: '',
        category_id: '',
        category_name: '',
        custcategory: '',
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
    $scope.formErrors = [];
    $scope.notsubmitable = false;
    $scope.formedit = true;
    $scope.bomgroups = [];
    $scope.getcurrency = {
        same_basecurrency: '',
        converted: '',
        base_symbol: ''
    }

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select..'
        });
        $('.selectcustcat').select2({
            placeholder: 'Select...'
        });
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    // init page load
    getPage(1, true);
    getCustcatOptions();
    getBomgroupSelectOptions();

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

    function getCustcatOptions() {
        $http.get('/custcat/data').success(function(data) {
            $scope.custcategories = data;
        });
    }

    $scope.removeEntry = function(id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this component and its parts?');
        if(isConfirmDelete){
            $http.delete('/api/bom/component/' + id + '/delete').success(function(data) {
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

    $scope.onCustcatChosen = function(bompart_id, custcategory_id) {
        $http.post('/api/bomtemplate/part/custcat', {bompart_id: bompart_id, custcategory_id: custcategory_id}).success(function(data) {
            getPage(1);
        });
    }

    $scope.onRemarkChanged = function(bompart_id, remark) {
        $http.post('/api/bompart/single/remark', {bompart_id: bompart_id, remark: remark}).success(function(data) {
            getPage(1);
        });
    }

    $scope.onQtyChanged = function(bompart_id, qty) {
        $http.post('/api/bompart/single/qty', {bompart_id: bompart_id, qty: qty}).success(function(data) {
            getPage(1);
        });
    }

    $scope.onBomcomponentRemarkChanged = function(bomcomponent_id, remark) {
        $http.post('/api/bomcomponent/single/remark', {bomcomponent_id: bomcomponent_id, remark: remark}).success(function(data) {
            getPage(1);
        });
    }

    $scope.onBomcomponentQtyChanged = function(bomcomponent_id, qty) {
        $http.post('/api/bomcomponent/single/qty', {bomcomponent_id: bomcomponent_id, qty: qty}).success(function(data) {
            getPage(1);
        });
    }

    $scope.removeBompart = function(bompart_id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this part?');
        if(isConfirmDelete){
            $http.delete('/api/bom/part/' + bompart_id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.passDataModal = function(bomcomponent) {
        $scope.partform = {
            title: bomcomponent.name,
            component_id: bomcomponent.component_id,
            bomcomponent_id : bomcomponent.id,
            part_id: $scope.getBompartIncrement(),
            bomgroup_id: '',
            name: '',
            qty: '',
            remark: '',
            supplier_order: '',
            unit_price: '',
            price_remark: '',
            pic: ''
        }
    }

    $scope.editDataModal = function(bompart) {
        fetchSingleBompart(bompart);
    }

    function fetchSingleBompart(bompart) {
        $scope.partform = {
            id: bompart.id,
            type: bompart.movable == 1 ? 'Consumable' : 'Part',
            color: bompart.movable == 1 ? '#fbfafc' : '#eae3f0',
            bomgroup_id: bompart.bomgroup_id,
            part_id: bompart.part_id,
            name: bompart.name,
            qty: bompart.qty,
            remark: bompart.remark,
            drawing_id: bompart.drawing_id,
            drawing_path: bompart.drawing_path,
            supplier_order: bompart.supplier_order,
            unit_price: bompart.unit_price,
            price_remark: bompart.price_remark,
            pic: bompart.pic
        }
    }

    $scope.createPart = function() {
        $http.post('/api/bomcomponent/bompart/create', $scope.partform).success(function(data) {
            getPage(1);
        });
    }

    $scope.editPart = function() {
        $http.post('/api/bompart/single/update', $scope.partform).success(function(data) {
            getPage(1);
        });
    }

    $scope.getBompartIncrement = function() {
        $http.get('/api/bompart_id/increment').success(function(data) {
            $scope.partform.part_id = data;
        });
    }

    $scope.syncCustcat = function(event, custcategory_id){
        event.preventDefault();
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
                $scope.form.custcategory_id = '';
                $('.selectcustcat').val(null).trigger('change.select2');
            });
        }else{
            return false;
        }
    }

    $scope.onPartIdChanged = function(part_id) {
        $http.post('/api/bompart_id/validate', {part_id: part_id})
            .success(function(data) {
                $scope.formErrors = [];
                $scope.notsubmitable = true;
            }).error(function(data) {
                $scope.formErrors = data;
                $scope.notsubmitable = false;
            });
    }

    $scope.editComponentModal = function(bomcomponent) {
        fetchSingleBomcomponent(bomcomponent);
    }

    function fetchSingleBomcomponent(bomcomponent) {
        $scope.componentform = {
            id: bomcomponent.id,
            bomgroup_id: bomcomponent.bomgroup_id,
            component_id: bomcomponent.component_id,
            drawing_id: bomcomponent.drawing_id,
            drawing_path: bomcomponent.drawing_path,
            name: bomcomponent.name,
            remark: bomcomponent.remark,
            supplier_order: bomcomponent.supplier_order,
            unit_price: bomcomponent.unit_price,
            price_remark: bomcomponent.price_remark,
            pic: bomcomponent.pic
        }
    }

    $scope.editComponent = function() {
        $http.post('/api/bomcomponent/single/update', $scope.componentform).success(function(data) {
            getPage(1);
        });
    }

    $scope.errors = [];
    $scope.files = [];
    var formData = new FormData();

    $scope.uploadBomcomponentFile = function (bomcomponent_id) {
        $scope.editComponent();
        var request = {
            method: 'POST',
            url: '/bomcomponent/drawing/upload/' + bomcomponent_id,
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#component_file');
                fileElement.value = '';
                alert("Image has been uploaded successfully!");
                $http.get('/api/bomcomponent/' + bomcomponent_id).success(function(data) {
                    fetchSingleBomcomponent(data);
                });
            }, function error(e) {
                $scope.errors = e.data.errors;
            });
    };

    $scope.setTheBomcomponentFiles = function ($files) {
        angular.forEach($files, function (value, key) {
            formData.append('component_file', value);
        });
    };

    $scope.deleteBomcomponentDrawing = function(bomcomponent_id) {
        $http.delete('/api/bomcomponent/drawing/'+ bomcomponent_id + '/delete').success(function(data) {
            $http.get('/api/bomcomponent/' + bomcomponent_id).success(function(comdata) {
                fetchSingleBomcomponent(comdata);
            });
        });
    }

    $scope.uploadBompartFile = function (bompart_id) {
        $scope.editPart();
        var request = {
            method: 'POST',
            url: '/bompart/drawing/upload/' + bompart_id,
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#part_file');
                fileElement.value = '';
                alert("Image has been uploaded successfully!");
                $http.get('/api/bompart/' + bompart_id).success(function(data) {
                    fetchSingleBompart(data);
                });
            }, function error(e) {
                $scope.errors = e.data.errors;
            });
    };

    $scope.setTheBompartFiles = function ($files) {
        angular.forEach($files, function (value, key) {
            formData.append('part_file', value);
        });
    };

    $scope.deleteBompartDrawing = function(bompart_id) {
        $http.delete('/api/bompart/drawing/'+ bompart_id + '/delete').success(function(data) {
            $http.get('/api/bompart/' + bompart_id).success(function(partdata) {
                fetchSingleBompart(partdata);
            });
        });
    }

    $scope.onBomcomponentCustcatChosen = function(bomcomponent_id, custcategory_id) {
        $http.post('/api/bomcomponent/custcat', {bomcomponent_id: bomcomponent_id, custcategory_id: custcategory_id}).success(function(data) {
            getPage(1);
        });
    }

    $scope.passBompartconsumableModal = function(bompart) {
        $scope.conpartform = {
            part_name: bompart.name,
            part_id: bompart.part_id,
            bompart_id: bompart.id,
            bomgroup_id: '',
            bompartconsumable_id: $scope.getBompartconsumableIncrement(),
            name: '',
            qty: '',
            remark: '',
            supplier_order: '',
            unit_price: '',
            price_remark: '',
            pic: ''
        }
    }

    $scope.getBompartconsumableIncrement = function() {
        $http.get('/api/bompartconsumable_id/increment').success(function(data) {
            $scope.conpartform.bompartconsumable_id = data;
        });
    }

    $scope.editBompartconsumableModal = function(bompartconsumable) {
        fetchSingleBompartconsumable(bompartconsumable);
    }

    function fetchSingleBompartconsumable(bompartconsumable) {
        $scope.conpartform = {
            id: bompartconsumable.id,
            bompartconsumable_id: bompartconsumable.partconsumable_id,
            bomgroup_id: bompartconsumable.bomgroup_id,
            bompart_id: bompartconsumable.bompart_id,
            name: bompartconsumable.name,
            qty: bompartconsumable.qty,
            remark: bompartconsumable.remark,
            drawing_id: bompartconsumable.drawing_id,
            drawing_path: bompartconsumable.drawing_path,
            supplier_order: bompartconsumable.supplier_order,
            unit_price: bompartconsumable.unit_price,
            price_remark: bompartconsumable.price_remark,
            pic: bompartconsumable.pic
        }
    }

    $scope.onBompartconsumableRemarkChanged = function(bompartconsumable_id, remark) {
        $http.post('/api/bompartconsumable/single/remark', {bompartconsumable_id: bompartconsumable_id, remark: remark}).success(function(data) {
            getPage(1);
        });
    }

    $scope.onBompartconsumableQtyChanged = function(bompartconsumable_id, qty) {
        $http.post('/api/bompartconsumable/single/qty', {bompartconsumable_id: bompartconsumable_id, qty: qty}).success(function(data) {
            getPage(1);
        });
    }

    $scope.removeBompartconsumable = function(bompartconsumable_id) {
        var isConfirmDelete = confirm('Are you sure to DELETE consumable from thispart?');
        if(isConfirmDelete){
            $http.delete('/api/bom/bompartconsumable/' + bompartconsumable_id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.uploadBompartconsumableFile = function (bompartconsumable_id) {
        $scope.editBompartconsumable();
        var request = {
            method: 'POST',
            url: '/bompartconsumable/drawing/upload/' + bompartconsumable_id,
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#bompartconsumable_file');
                fileElement.value = '';
                alert("Image has been uploaded successfully!");
                $http.get('/api/bompartconsumable/' + bompartconsumable_id).success(function(data) {
                    fetchSingleBompartconsumable(data);
                });
            }, function error(e) {
                $scope.errors = e.data.errors;
            });
    };

    $scope.setTheBompartconsumableFiles = function ($files) {
        angular.forEach($files, function (value, key) {
            formData.append('bompartconsumable_file', value);
        });
    };

    $scope.deleteBompartconsumableDrawing = function(bompartconsumable_id) {
        $http.delete('/api/bompartconsumable/drawing/'+ bompartconsumable_id + '/delete').success(function(data) {
            $http.get('/api/bompartconsumable/' + bompartconsumable_id).success(function(conpartdata) {
                fetchSingleBompartconsumable(conpartdata);
            });
        });
    }

    $scope.createBompartconsumable = function() {
        $http.post('/api/bompartconsumable/create', $scope.conpartform).success(function(data) {
            getPage(1);
        });
    }

    $scope.editBompartconsumable = function() {
        $http.post('/api/bompartconsumable/update', $scope.conpartform).success(function(data) {
            getPage(1);
        });
    }

    $scope.onBompartconsumableCustcatChosen = function(bompartconsumable_id, custcategory_id) {
        $http.post('/api/bompartconsumable/custcat', {bompartconsumable_id: bompartconsumable_id, custcategory_id: custcategory_id}).success(function(data) {
            getPage(1);
        });
    }

    function getBomgroupSelectOptions() {
        $http.get('/api/bom/groups/all').success(function(data) {
            $scope.bomgroups = data;
        });
    }

    $scope.onFromCustcategoryIdChanged = function(from_custcategory_id) {
        $http.get('/api/tocustcategory/' + from_custcategory_id).success(function(data) {
            $scope.to_custcategories = data;
        });
    }

    $scope.replicateCuscatBinding = function(event) {
        event.preventDefault();
        $http.post('/api/bom/replicate/custcat', {
            from_custcategory_id: $scope.form.from_custcategory_id,
            to_custcategory_id: $scope.form.to_custcategory_id
        }).success(function(data) {
            $scope.form.to_custcategory_id = '';
            $scope.form.from_custcategory_id = '';
            $('.selectcustcat').val(null).trigger('change.select2');
            getPage(1);
        });
    }

    $scope.clearGetCurrency = function() {
        $scope.getcurrency = {
            same_basecurrency: '',
            converted: '',
            base_symbol: ''
        }
    }

    $scope.retrieveGetCurrency = function() {
        $http.post
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
        custcategory: '',
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

function bomgroupController($scope, $http, $window){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        prefix: '',
        name: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        id: '',
        prefix: '',
        name: '',
        remark: ''
    }
    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomgroup').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "BOM Group"+ now + ".xls");
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
        $http.post('/api/bom/groups?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.bomgroups.data){
                $scope.alldata = data.bomgroups.data;
                $scope.totalCount = data.bomgroups.total;
                $scope.currentPage = data.bomgroups.current_page;
                $scope.indexFrom = data.bomgroups.from;
                $scope.indexTo = data.bomgroups.to;
            }else{
                $scope.alldata = data.bomgroups;
                $scope.totalCount = data.bomgroups.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bomgroups.length;
            }
            $scope.All = data.bomgroups.length;
            $scope.spinner = false;
        });
    }

    $scope.createBomgroupModal = function() {
        $scope.form = {
            id: '',
            prefix: '',
            name: '',
            remark: ''
        }
    }


    $scope.createBomgroup = function() {
        $http.post('/api/bom/group/create', $scope.form). success(function(data) {
            getPage(1);

            $scope.form = {
                id: '',
                prefix: '',
                name: '',
                remark: ''
            }
        }).error(function(data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function(id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this group, its parts and consumables?');
        if(isConfirmDelete){
            $http.delete('/api/bom/group/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.editBomgroupModal = function(bomgroup) {
        fetchSingleBomgroup(bomgroup);
    }

    function fetchSingleBomgroup(bomgroup) {
        $scope.form = {
            id: bomgroup.id,
            prefix: bomgroup.prefix,
            name: bomgroup.name,
            remark: bomgroup.remark,
        }
    }

    $scope.editBomgroup = function() {
        $http.post('/api/bom/group/update', $scope.form).success(function(data) {
            getPage(1);
        });
    }
}

function bomcurrencyController($scope, $http, $window){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        name: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        id: '',
        symbol: '',
        name: '',
        rate: ''
    }
    // init page load
    getPage(1, true);

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_bomcurrency').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Currencies"+ now + ".xls");
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
        $http.post('/api/bom/currencies?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.bomcurrencies.data){
                $scope.alldata = data.bomcurrencies.data;
                $scope.totalCount = data.bomcurrencies.total;
                $scope.currentPage = data.bomcurrencies.current_page;
                $scope.indexFrom = data.bomcurrencies.from;
                $scope.indexTo = data.bomcurrencies.to;
            }else{
                $scope.alldata = data.bomcurrencies;
                $scope.totalCount = data.bomcurrencies.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.bomcurrencies.length;
            }
            $scope.All = data.bomcurrencies.length;
            $scope.spinner = false;
        });
    }

    $scope.createBomcurrencyModal = function() {
        $scope.form = {
            id: '',
            symbol: '',
            name: '',
            rate: ''
        }
    }


    $scope.createBomcurrency = function() {
        $http.post('/api/bom/currency/create', $scope.form). success(function(data) {
            getPage(1);

            $scope.form = {
                id: '',
                symbol: '',
                name: '',
                rate: ''
            }
        }).error(function(data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function(id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this currency?');
        if(isConfirmDelete){
            $http.delete('/api/bom/currency/' + id + '/delete').success(function(data) {
                getPage(1);
            });
        }else{
            return false;
        }
    }

    $scope.editBomcurrencyModal = function(bomcurrency) {
        fetchSingleBomcurrency(bomcurrency);
    }

    function fetchSingleBomcurrency(bomcurrency) {
        $scope.form = {
            id: bomcurrency.id,
            symbol: bomcurrency.symbol,
            name: bomcurrency.name,
            rate: bomcurrency.rate,
        }
    }

    $scope.editBomcurrency = function() {
        $http.post('/api/bom/currency/update', $scope.form).success(function(data) {
            getPage(1);
        });
    }

    $scope.onBomcurrencyRateChanged = function(bomcurrency_id, rate) {
        console.log($scope.form.rate);
        $http.post('/api/bom/currency/' + bomcurrency_id + '/rate', {rate: rate}).success(function(data) {
            getPage(1);
        });
    }
}



app.controller('bomCategoryController', bomCategoryController);
app.controller('bomComponentController', bomComponentController);
app.controller('bomPartController', bomPartController);
app.controller('bomTemplateController', bomTemplateController);
app.controller('bomVendingController', bomVendingController);
app.controller('maintenanceController', maintenanceController);
app.controller('bomgroupController', bomgroupController);
app.controller('bomcurrencyController', bomcurrencyController);

app.directive('ngFiles', ['$parse', function ($parse) {
    function file_links(scope, element, attrs) {
        var onChange = $parse(attrs.ngFiles);
        element.on('change', function (event) {
            onChange(scope, {$files: event.target.files});
        });
    }
    return {
        link: file_links
    }
}]);

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
