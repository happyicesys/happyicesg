var app = angular.module('app', [
    'ui.bootstrap',
    'angularUtils.directives.dirPagination',
    ]);

    function userController($scope, $http){

        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;
        $scope.currentPage2 = 1;
        $scope.itemsPerPage2 = 10;
        $scope.currentPage3 = 1;
        $scope.itemsPerPage3 = 10;
        $scope.currentPage4 = 1;
        $scope.itemsPerPage4 = 10;

        $http.get('/user/data').success(function(users){
            $scope.users = users;
        });

        $http.get('/freezer/data').success(function(freezers){
            $scope.freezers = freezers;
        });

        $http.get('/accessory/data').success(function(accessories){
            $scope.accessories = accessories;
        });

        $http.get('/payterm/data').success(function(payterms){
            $scope.payterms = payterms;
        });

        //delete record
        $scope.confirmDelete = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/user/data/' + id
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

        $scope.confirmDelete2 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/freezer/data/' + id
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

        $scope.confirmDelete3 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/accessory/data/' + id
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

        $scope.confirmDelete4 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/payterm/data/' + id
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

    function custCategoryController($scope, $http) {
        $scope.currentPage5 = 1;
        $scope.itemsPerPage5 = 100;

        $http.get('/custcat/data').success(function(custcats) {
            $scope.custcats = custcats;
        });

        $scope.confirmDelete5 = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/custcat/data/' + id
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

    function custTagsController($scope, $http) {
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            tag_name: '',
            cust_id: '',
            company: '',
            pageNum: 100,
            sortBy: true,
            sortName: ''
        }
        // init page load
        getPage();

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
        });

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable_cust_tags').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Cust Tags"+ now + ".xls");
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
            $http.post('/api/custtags/index/1?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.alldeals.data){
                    $scope.alldata = data.alldeals.data;
                    $scope.totalCount = data.alldeals.total;
                    $scope.currentPage = data.alldeals.current_page;
                    $scope.indexFrom = data.alldeals.from;
                    $scope.indexTo = data.alldeals.to;
                }else{
                    $scope.alldata = data.alldeals;
                    $scope.totalCount = data.alldeals.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.alldeals.length;
                }
                $scope.spinner = false;
            });
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

function repeatController3($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage3 - 1) * $scope.itemsPerPage3;
    })
}

function repeatController4($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage4 - 1) * $scope.itemsPerPage4;
    })
}

function repeatController5($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage5 - 1) * $scope.itemsPerPage5;
    })
}

app.controller('userController', userController);
app.controller('custCategoryController', custCategoryController);
app.controller('repeatController', repeatController);
app.controller('repeatController2', repeatController2);
app.controller('repeatController3', repeatController3);
app.controller('repeatController4', repeatController4);
app.controller('repeatController5', repeatController5);
app.controller('custTagsController', custTagsController);

$(function() {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('lastTab', $(this).attr('href'));
    });
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
});






