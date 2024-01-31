var app = angular.module('app', [
    'ui.bootstrap',
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize'
]);

function userController($scope, $http) {

    $scope.currentPage = 1;
    $scope.itemsPerPage = 10;
    $scope.currentPage2 = 1;
    $scope.itemsPerPage2 = 10;
    $scope.currentPage3 = 1;
    $scope.itemsPerPage3 = 10;
    $scope.currentPage4 = 1;
    $scope.itemsPerPage4 = 10;

    $http.get('/user/data').success(function (users) {
        $scope.users = users;
    });

    $http.get('/freezer/data').success(function (freezers) {
        $scope.freezers = freezers;
    });

    $http.get('/accessory/data').success(function (accessories) {
        $scope.accessories = accessories;
    });

    $http.get('/payterm/data').success(function (payterms) {
        $scope.payterms = payterms;
    });

    //delete record
    $scope.confirmDelete = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/user/data/' + id
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

    $scope.confirmDelete2 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/freezer/data/' + id
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

    $scope.confirmDelete3 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/accessory/data/' + id
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

    $scope.confirmDelete4 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/payterm/data/' + id
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

function custCategoryController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        name: '',
        custcategories: [],
        active: ['Yes'],
        pageNum: 'All',
        sortBy: true,
        sortName: ''
    }
    $scope.form = {
        name: '',
        desc: '',
    }
    $scope.custcategory = [];
    let map_icon_base = 'http://maps.google.com/mapfiles/ms/micons/';
    const MAP_ICON_FILE = {
        'red': 'red.png',
        'blue': 'blue.png',
        'green': 'green.png',
        'light-blue': 'lightblue.png',
        'pink': 'pink.png',
        'purple': 'purple.png',
        'yellow': 'yellow.png',
        'orange': 'orange.png'
    };
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
        var blob = new Blob(["\ufeff", document.getElementById('exportable_custcategory').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Custcategory" + now + ".xls");
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
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    $scope.onCustcategoryDelete = function (data) {
        var isConfirmDelete = confirm('Are you sure you want to delete the custcategory & detach its binding(s)?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/custcat/data/' + data.id
            })
                .success(function (data) {
                    getPage(1, false);
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    // create
    $scope.onCustcategoryGroupNameCreateClicked = function () {
        $http.post('/api/custcat/group/create', $scope.form).success(function (data) {
            $scope.form.name = '';
            getPage(1, false);
        });
    }

    // bind
    $scope.onCustcategoryGroupBindingClicked = function () {
        $http.post('/api/custcat/group/bind', $scope.form).success(function (data) {
            $scope.form.custcategory_id = '';
            getPage(1, false);
        });
    }

    $scope.onAttachmentModalClicked = function (event, custcategory) {
        event.preventDefault();
        $scope.custcategory = custcategory;
    }

    $scope.removeAttachment = function (event, custcategoryId, attachmentId) {
        event.preventDefault();
        $http.post('/api/custcat/' + custcategoryId + '/attachment/' + attachmentId + '/delete').success(function (data) {
            getPage(1, false);
            // location.reload();
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/custcat/data?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.custcategories.data) {
                $scope.alldata = data.custcategories.data;
                $scope.totalCount = data.custcategories.total;
                $scope.currentPage = data.custcategories.current_page;
                $scope.indexFrom = data.custcategories.from;
                $scope.indexTo = data.custcategories.to;
            } else {
                $scope.alldata = data.custcategories;
                $scope.totalCount = data.custcategories.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.custcategories.length;
            }
            $scope.spinner = false;
            angular.forEach($scope.alldata, function (value, key) {
                $scope.alldata[key].map_icon_file = map_icon_base + MAP_ICON_FILE[value.map_icon_file]
            });
        });
    }
}

function custPrefixController($scope, $http) {
    $scope.currentPage13 = 1;
    $scope.itemsPerPage13 = 100;

    $http.get('/api/cust-prefixes').success(function (custPrefixes) {
        $scope.custPrefixes = custPrefixes;
    });

    // export cust cat excel
    $scope.exportCustPrefixExcel = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable_cust_prefix').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Cust Prefix" + now + ".xls");
    };

    $scope.confirmDelete13 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete this cust prefix?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/cust-prefixes/data/' + id
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
        saveAs(blob, "Cust Tags" + now + ".xls");
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
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    $scope.onTagDelete = function (tag) {
        var isConfirmDelete = confirm('Are you sure you want to delete the tag with its binding(s)?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/api/person/custtag/' + tag.id + '/destroy'
            })
                .success(function (data) {
                    getPage(1, false);
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    $scope.onTagUnbind = function (persontagattach) {
        $http({
            method: 'POST',
            url: '/api/person/custtagattach/' + persontagattach.id + '/unbind'
        })
            .success(function (data) {
                getPage(1, false);
            })
            .error(function (data) {
                alert('Unable to delete');
            })
    }

    // create new tag name
    $scope.onTagNameCreateClicked = function () {
        $http.post('/api/persontag/create', $scope.form).success(function (data) {
            $scope.form.persontag_name = '';
            getPage(1, false);
        });
    }

    // bind user id and tag id
    $scope.onTagBindingClicked = function () {
        $http.post('/api/persontagattaches/bind', $scope.form).success(function (data) {
            $scope.form.person_id = '';
            getPage(1, false);
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/person/custtags?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.custtags.data) {
                $scope.alldata = data.custtags.data;
                $scope.totalCount = data.custtags.total;
                $scope.currentPage = data.custtags.current_page;
                $scope.indexFrom = data.custtags.from;
                $scope.indexTo = data.custtags.to;
            } else {
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

function custCategoryGroupController($scope, $http) {
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
        name: '',
        custcategories: [],
        pageNum: 100,
        sortBy: true,
        sortName: ''
    }
    $scope.form = {
        name: '',
        desc: '',
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
        var blob = new Blob(["\ufeff", document.getElementById('exportable_custcategory_group').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Custcategory Group" + now + ".xls");
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
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    $scope.onCustcategoryGroupDelete = function (data) {
        var isConfirmDelete = confirm('Are you sure you want to delete the custcategory group & detach its binding(s)?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/api/custcat/group/' + data.id + '/destroy'
            })
                .success(function (data) {
                    getPage(1, false);
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    $scope.onCustcategoryGroupUnbind = function (id) {
        $http({
            method: 'POST',
            url: '/api/custcat/group/' + id + '/unbind'
        })
            .success(function (data) {
                getPage(1, false);
            })
            .error(function (data) {
                alert('Unable to delete');
            })
    }

    // create
    $scope.onCustcategoryGroupNameCreateClicked = function () {
        $http.post('/api/custcat/group/create', $scope.form).success(function (data) {
            $scope.form.name = '';
            getPage(1, false);
        });
    }

    // bind
    $scope.onCustcategoryGroupBindingClicked = function () {
        $http.post('/api/custcat/group/bind', $scope.form).success(function (data) {
            $scope.form.custcategory_id = '';
            getPage(1, false);
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/custcat/group?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.custcategoryGroups.data) {
                $scope.alldata = data.custcategoryGroups.data;
                $scope.totalCount = data.custcategoryGroups.total;
                $scope.currentPage = data.custcategoryGroups.current_page;
                $scope.indexFrom = data.custcategoryGroups.from;
                $scope.indexTo = data.custcategoryGroups.to;
            } else {
                $scope.alldata = data.custcategoryGroups;
                $scope.totalCount = data.custcategoryGroups.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.custcategoryGroups.length;
            }
            $scope.spinner = false;
        });
    }
}

function itemGroupController($scope, $http) {
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
        itemGroups: [],
        pageNum: 100,
        sortBy: true,
        sortName: ''
    }
    $scope.form = {
        name: '',
        desc: '',
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
        var blob = new Blob(["\ufeff", document.getElementById('exportable_item_group').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Item Group" + now + ".xls");
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
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    $scope.onItemGroupDelete = function (data) {
        var isConfirmDelete = confirm('Are you sure you want to delete the item group & detach its binding(s)?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/api/item/group/' + data.id + '/destroy'
            })
                .success(function (data) {
                    getPage(1, false);
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    $scope.onItemGroupUnbind = function (id) {
        $http({
            method: 'POST',
            url: '/api/item/group/' + id + '/unbind'
        })
            .success(function (data) {
                getPage(1, false);
            })
            .error(function (data) {
                alert('Unable to delete');
            })
    }

    // create
    $scope.onItemGroupNameCreateClicked = function () {
        $http.post('/api/item/group/create', $scope.form).success(function (data) {
            $scope.form.name = '';
            getPage(1, false);
        });
    }

    // bind
    $scope.onItemGroupBindingClicked = function () {
        $http.post('/api/item/group/bind', $scope.form).success(function (data) {
            $scope.form.custcategory_id = '';
            getPage(1, false);
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/item/group?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.itemGroups.data) {
                $scope.alldata = data.itemGroups.data;
                $scope.totalCount = data.itemGroups.total;
                $scope.currentPage = data.itemGroups.current_page;
                $scope.indexFrom = data.itemGroups.from;
                $scope.indexTo = data.itemGroups.to;
            } else {
                $scope.alldata = data.itemGroups;
                $scope.totalCount = data.itemGroups.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.itemGroups.length;
            }
            $scope.spinner = false;
        });
    }
}

function itemcategoryController($scope, $http) {
    $scope.currentPage6 = 1;
    $scope.itemsPerPage6 = 100;

    $http.get('/api/itemcategories').success(function (itemcategories) {
        $scope.itemcategories = itemcategories;
    });

    // export cust cat excel
    $scope.exportItemcategoryExcel = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable_itemcategory').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Itemcategory" + now + ".xls");
    };

    $scope.confirmDelete6 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/itemcategory/data/' + id
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

function truckController($scope, $http) {
    $scope.currentPage10 = 1;
    $scope.itemsPerPage10 = 100;

    $http.get('/api/trucks').success(function (trucks) {
        $scope.trucks = trucks;
    });

    // export cust cat excel
    $scope.exportTruckExcel = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable_truck').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Truck" + now + ".xls");
    };

    $scope.confirmDelete10 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete this truck?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/truck/data/' + id
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

function zoneController($scope, $http) {
    $scope.currentPage11 = 1;
    $scope.itemsPerPage11 = 100;

    $http.get('/api/zones/all').success(function (zones) {
        $scope.zones = zones;
    });

    // export cust cat excel
    $scope.exportZoneExcel = function (event) {
        event.preventDefault();
        var blob = new Blob(["\ufeff", document.getElementById('exportable_zone').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Zone" + now + ".xls");
    };

    $scope.confirmDelete11 = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete this zone?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/zone/data/' + id
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

function rackingConfigController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        name: '',
        desc: '',
        pageNum: 'All',
        sortBy: true,
        sortName: ''
    }
    $scope.form = {
        name: '',
        desc: '',
    }
    $scope.rackingConfig = [];
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
        var blob = new Blob(["\ufeff", document.getElementById('exportable_racking_config').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Racking Config" + now + ".xls");
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
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    $scope.onRackingConfigDelete = function (data) {
        var isConfirmDelete = confirm('Are you sure you want to delete the racking configs and its binding(s)?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/racking-configs/data/' + data.id
            })
                .success(function (data) {
                    getPage(1, false);
                })
                .error(function (data) {
                    alert('Unable to delete');
                })
        } else {
            return false;
        }
    }

    $scope.onAttachmentModalClicked = function (event, rackingConfig) {
        event.preventDefault();
        $scope.rackingConfig = rackingConfig;
    }

    $scope.removeAttachment = function (event, rackingConfigId, attachmentId) {
        event.preventDefault();
        $http.post('/api/racking-configs/' + rackingConfigId + '/attachment/' + attachmentId + '/delete').success(function (data) {
            getPage(1, false);
            // location.reload();
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/racking-configs/data?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.rackingConfigs.data) {
                $scope.alldata = data.rackingConfigs.data;
                $scope.totalCount = data.rackingConfigs.total;
                $scope.currentPage = data.rackingConfigs.current_page;
                $scope.indexFrom = data.rackingConfigs.from;
                $scope.indexTo = data.rackingConfigs.to;
            } else {
                $scope.alldata = data.rackingConfigs;
                $scope.totalCount = data.rackingConfigs.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.rackingConfigs.length;
            }
            $scope.spinner = false;
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

function repeatController3($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage3 - 1) * $scope.itemsPerPage3;
    })
}

function repeatController4($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage4 - 1) * $scope.itemsPerPage4;
    })
}

function repeatController5($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage5 - 1) * $scope.itemsPerPage5;
    })
}

function repeatController6($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage6 - 1) * $scope.itemsPerPage6;
    })
}

function repeatController10($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage10 - 1) * $scope.itemsPerPage10;
    })
}

function repeatController11($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage11 - 1) * $scope.itemsPerPage11;
    })
}

function repeatController12($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage12 - 1) * $scope.itemsPerPage12;
    })
}

function repeatController13($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage13 - 1) * $scope.itemsPerPage13;
    })
}


app.controller('userController', userController);
app.controller('custCategoryController', custCategoryController);
app.controller('custPrefixController', custPrefixController);
app.controller('repeatController', repeatController);
app.controller('repeatController2', repeatController2);
app.controller('repeatController3', repeatController3);
app.controller('repeatController4', repeatController4);
app.controller('repeatController5', repeatController5);
app.controller('repeatController6', repeatController6);
app.controller('repeatController10', repeatController10);
app.controller('repeatController11', repeatController11);
app.controller('repeatController12', repeatController12);
app.controller('repeatController13', repeatController13);
app.controller('custTagsController', custTagsController);
app.controller('custCategoryGroupController', custCategoryGroupController);
app.controller('itemcategoryController', itemcategoryController);
app.controller('itemGroupController', itemGroupController);
app.controller('truckController', truckController);
app.controller('zoneController', zoneController);
app.controller('rackingConfigController', rackingConfigController);

$(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('lastTab', $(this).attr('href'));
    });
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
});

app.directive('ngConfirmClick', [
    function () {
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmClick || "Are you sure?";
                var clickAction = attr.confirmedClick;
                element.bind('click', function (event) {
                    if (window.confirm(msg)) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
    }]);

app.filter('trusted', ['$sce', function ($sce) {
    return function (url) {
        return $sce.trustAsResourceUrl(url);
    };
}]);






