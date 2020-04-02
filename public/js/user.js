var app = angular.module('app', [
    'ui.bootstrap',
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize'
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
        $scope.peopledata = [];
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
        $scope.form = {
            persontag_name: '',
            persontag_id: '',
            person_id: ''
        }
        // init page load
        getPage();

        angular.element(document).ready(function () {
            $('.select').select2({
                placeholder: 'Select..'
            });
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

        $scope.onTagDelete = function(tag) {
            var isConfirmDelete = confirm('Are you sure you want to delete the tag with its binding(s)?');
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/api/person/custtag/' + tag.id + '/destroy'
                })
                .success(function(data){
                    getPage(1, false);
                })
                .error(function(data){
                    alert('Unable to delete');
                })
            }else{
                return false;
            }
        }

        $scope.onTagUnbind = function(persontagattach) {
            $http({
                method: 'POST',
                url: '/api/person/custtagattach/' + persontagattach.id + '/unbind'
            })
            .success(function(data){
                getPage(1, false);
            })
            .error(function(data){
                alert('Unable to delete');
            })
        }

        // create new tag name
        $scope.onTagNameCreateClicked = function() {
            $http.post('/api/persontag/create', $scope.form).success(function(data){
                $scope.form.persontag_name = '';
                getPage(1, false);
            });
        }

        // bind user id and tag id
        $scope.onTagBindingClicked = function() {
            $http.post('/api/persontagattaches/bind', $scope.form).success(function(data) {
                $scope.form.person_id = '';
                getPage(1, false);
            });
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/person/custtags?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.custtags.data){
                    $scope.alldata = data.custtags.data;
                    $scope.totalCount = data.custtags.total;
                    $scope.currentPage = data.custtags.current_page;
                    $scope.indexFrom = data.custtags.from;
                    $scope.indexTo = data.custtags.to;
                }else{
                    $scope.alldata = data.custtags;
                    $scope.totalCount = data.custtags.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.custtags.length;
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






