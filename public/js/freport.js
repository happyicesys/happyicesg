var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.bootstrap.datetimepicker']);

function analogDifferenceController($scope, $http){

    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "InventoryRpt"+ now + ".xls");
    };

    $http.get('/api/pricematrix').success(function(data){
        // console.log(data);
        $scope.items = data.items;
        $scope.people = data.people;
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

      // when hitting search button
    $scope.searchDB = function(){
        getPage();
    }

    // retrieve page w/wo search
    function getPage(pageNumber){
        $scope.spinner = true;
        $http.post('/api/pricematrix', $scope.search).success(function(data){
            $scope.items = data.items;
            $scope.people = data.people;
        });
    }
}


function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}


app.controller('analogDifferenceController', analogDifferenceController);
app.controller('repeatController', repeatController);

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
