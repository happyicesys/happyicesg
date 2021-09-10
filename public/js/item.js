var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.bootstrap.datetimepicker', 'angularUtils.directives.dirPagination']);

function itemController($scope, $http) {

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
        product_id: '',
        name: '',
        remark: '',
        is_active: 1,
        is_inventory: 1,
        pageNum: 100
    }

    $scope.currentPage2 = 1;
    $scope.itemsPerPage2 = 100;


    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_item').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Items" + now + ".xls");
    };

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function () {
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1, false)
    };

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/items?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.items.data) {
                $scope.alldata = data.items.data;
                $scope.totalCount = data.items.total;
                $scope.currentPage = data.items.current_page;
                $scope.indexFrom = data.items.from;
                $scope.indexTo = data.items.to;
            } else {
                $scope.alldata = data.items;
                $scope.totalCount = data.items.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.items.length;
            }
            // get total count
            $scope.All = data.items.length;
            $scope.totals = data.totals;

            // return total amount
            $scope.spinner = false;
        });
    }

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "InventoryRpt" + now + ".xls");
    };

    $http.get('/item/data').success(function (data) {
        $scope.items = data.items;
        $scope.total_available = data.total_available;
        $scope.total_booked = data.total_booked;
        $scope.All = data.items.length;
    });

    $http.get('/inventory/data').success(function (inventories) {
        $scope.inventories = inventories;
        $scope.All = inventories.length;
    });

    $scope.dateChange3 = function (date) {
        $scope.search2.rec_date = moment(date).format("YYYY-MM-DD");
    }

    $scope.dateChange2 = function (date) {
        $scope.search2.created_at = moment(date).format("YYYY-MM-DD");
    }

    //delete item record
    $scope.confirmDelete = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/item/data/' + id
            })
                .success(function (data) {
                    location.reload();
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    //delete inventory record
    $scope.confirmDelete2 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/inventory/data/' + id
            })
                .success(function (data) {
                    location.reload();
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }
}

function itemNonInventoryController($scope, $http) {

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
        product_id: '',
        name: '',
        remark: '',
        is_active: 1,
        is_inventory: 0,
        is_supermarket_fee: 0,
        is_commission: 0,
        pageNum: 100
    }

    $scope.currentPage2 = 1;
    $scope.itemsPerPage2 = 100;


    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_item').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Items" + now + ".xls");
    };

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function () {
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1, false)
    };

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/items?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            // console.log(JSON.parse(JSON.stringify($scope.search)));
            // console.log(JSON.parse(JSON.stringify(data.items.data)));
            if (data.items.data) {
                $scope.alldata = data.items.data;
                $scope.totalCount = data.items.total;
                $scope.currentPage = data.items.current_page;
                $scope.indexFrom = data.items.from;
                $scope.indexTo = data.items.to;
            } else {
                $scope.alldata = data.items;
                $scope.totalCount = data.items.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.items.length;
            }
            // get total count
            $scope.All = data.items.length;
            $scope.totals = data.totals;

            // return total amount
            $scope.spinner = false;
        });
    }

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "InventoryRpt" + now + ".xls");
    };

    $http.get('/item/data').success(function (data) {
        $scope.items = data.items;
        $scope.total_available = data.total_available;
        $scope.total_booked = data.total_booked;
        $scope.All = data.items.length;
    });

    $http.get('/inventory/data').success(function (inventories) {
        $scope.inventories = inventories;
        $scope.All = inventories.length;
    });

    $scope.dateChange3 = function (date) {
        $scope.search2.rec_date = moment(date).format("YYYY-MM-DD");
    }

    $scope.dateChange2 = function (date) {
        $scope.search2.created_at = moment(date).format("YYYY-MM-DD");
    }

    //delete item record
    $scope.confirmDelete = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/item/data/' + id
            })
                .success(function (data) {
                    location.reload();
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    //delete inventory record
    $scope.confirmDelete2 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/inventory/data/' + id
            })
                .success(function (data) {
                    location.reload();
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }
}

function unitcostController($scope, $http) {
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


        $('#checkAll').change(function () {
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_unitcost').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Unit Cost" + now + ".xls");
    };

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage();
    }

    // when hitting search button
    $scope.searchDB = function () {
        getPage();
    }
    /*
        $scope.getUnitcostInit = function(profile_id, item_id) {
            $http.get('/api/unitcost/' + profile_id + '/' + item_id).success(function(response) {
                return
            });
        }*/

    // retrieve page w/wo search
    function getPage(pageNumber) {
        $scope.spinner = true;
        $http.post('/api/item/unitcost', $scope.search).success(function (data) {
            /*             $scope.items = data.items;
                        $scope.profiles = data.profiles;
                        $scope.unitcosts = data.unitcosts; */
            $scope.alldata = data.dataArr;
        });
        $scope.totalCount = 0;
        $scope.spinner = false;
        /*         $scope.countInit = function() {
                   return $scope.totalCount++;
                }
                $scope.getUnitcostInit = function(profile_id, item_id){
                    for(var i = 0; i < $scope.unitcosts.length; i ++){
                        var unitcost = 0;
                        if($scope.unitcosts[i].profile_id == profile_id && $scope.unitcosts[i].item_id == item_id){
                            return $scope.unitcosts[i].unit_cost;
                        }
                    }
                } */
    }
}


function priceMatrixController($scope, $http) {

    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "InventoryRpt" + now + ".xls");
    };

    $http.get('/api/pricematrix').success(function (data) {
        // console.log(data);
        $scope.items = data.items;
        $scope.people = data.people;
    });

    $http.get('/inventory/data').success(function (inventories) {
        $scope.inventories = inventories;
        $scope.All = inventories.length;
    });

    $scope.dateChange3 = function (date) {
        $scope.search2.rec_date = moment(date).format("YYYY-MM-DD");
    }

    $scope.dateChange2 = function (date) {
        $scope.search2.created_at = moment(date).format("YYYY-MM-DD");
    }

    //delete item record
    $scope.confirmDelete = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/item/data/' + id
            })
                .success(function (data) {
                    location.reload();
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    // when hitting search button
    $scope.searchDB = function () {
        getPage();
    }

    // lookup pricematrix
    /*    $scope.lookupPriceMatrixPrice = function(item_id, person_id) {
            $http.get('/api/prices/' + item_id + '/' + person_id).success(function(data) {
                console.log(data);
                return data;
            });
        }*/

    // retrieve page w/wo search
    function getPage(pageNumber) {
        $scope.spinner = true;
        $http.post('/api/pricematrix', $scope.search).success(function (data) {
            $scope.items = data.items;
            $scope.people = data.people;
        });
    }
}


function repeatController($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

function repeatController2($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage2 - 1) * $scope.itemsPerPage2;
    })
}


app.controller('itemController', itemController);
app.controller('itemNonInventoryController', itemNonInventoryController);
app.controller('unitcostController', unitcostController);
app.controller('priceMatrixController', priceMatrixController);
app.controller('repeatController', repeatController);
app.controller('repeatController2', repeatController2);

$(function () {
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
