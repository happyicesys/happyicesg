var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.bootstrap.datetimepicker']);

function itemController($scope, $http){

    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.currentPage2 = 1;
    $scope.itemsPerPage2 = 100;

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "InventoryRpt"+ now + ".xls");
    };

    $http.get('/item/data').success(function(data){
        $scope.items = data.items;
        $scope.total_available = data.total_available;
        $scope.total_booked = data.total_booked;
        $scope.All = data.items.length;
    });

    $http.get('/inventory/data').success(function(inventories){
        $scope.inventories = inventories;
        $scope.All = inventories.length;
    });

    $scope.dateChange3 = function(date){
        $scope.search2.rec_date = moment(date).format("YYYY-MM-DD");
    }

    $scope.dateChange2 = function(date){
        $scope.search2.created_at = moment(date).format("YYYY-MM-DD");
    }

    //delete item record
    $scope.confirmDelete = function(id){
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if(isConfirmDelete){
            $http({
                method: 'DELETE',
                url: '/item/data/' + id
            })
            .success(function(data){
                location.reload();
            })
            .error(function(data){
                alert('Unable to delete');
            })
        }else{
            return false;
        }
    }

    //delete inventory record
    $scope.confirmDelete2 = function(id){
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if(isConfirmDelete){
            $http({
                method: 'DELETE',
                url: '/inventory/data/' + id
            })
            .success(function(data){
                location.reload();
            })
            .error(function(data){
                alert('Unable to delete');
            })
        }else{
            return false;
        }
    }
}

function unitcostController($scope, $http){
    // init the variables
    $scope.items = [];
    $scope.profiles = [];
    $scope.search = {
        product_id: '',
        name: '',
        profile_id: '',
        sortName: '',
        sortBy: true
    }
    $scope.updated_at = '';
    // init page load
    getPage();

    angular.element(document).ready(function () {
        $('.select').select2();


        $('#checkAll').change(function(){
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_unitcost').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Unit Cost"+ now + ".xls");
    };

    $scope.sortTable = function(sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = ! $scope.search.sortBy;
        getPage();
    }

      // when hitting search button
    $scope.searchDB = function(){
        getPage();
    }
/*
    $scope.getUnitcostInit = function(profile_id, item_id) {
        $http.get('/api/unitcost/' + profile_id + '/' + item_id).success(function(response) {
            return
        });
    }*/

    // retrieve page w/wo search
    function getPage(pageNumber){
        $scope.spinner = true;
        $http.post('/api/item/unitcost', $scope.search).success(function(data){
            $scope.items = data.items;
            $scope.profiles = data.profiles;
            $scope.unitcosts = data.unitcosts;
            $scope.spinner = false;
        });
        $scope.totalCount = 0;
        $scope.countInit = function() {
           return $scope.totalCount++;
        }
        $scope.getUnitcostInit = function(profile_id, item_id){
            for(var i = 0; i < $scope.unitcosts.length; i ++){
                var unitcost = 0;
                if($scope.unitcosts[i].profile_id == profile_id && $scope.unitcosts[i].item_id == item_id){
                    return $scope.unitcosts[i].unit_cost;
                }
            }
        }
    }
}

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

function repeatController2($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage2 - 1) * $scope.itemsPerPage2;
    })
}


app.controller('itemController', itemController);
app.controller('unitcostController', unitcostController);
app.controller('repeatController', repeatController);
app.controller('repeatController2', repeatController2);

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
