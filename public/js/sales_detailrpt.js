var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker',
    'datePicker'
]);

function custDetailController($scope, $http) {
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
        edited: false,
        profile_id: '',
        current_month: moment().month() + 1 + '-' + moment().year(),
        id_prefix: '',
        cust_id: '',
        company: '',
        custcategory: [],
        exACategory: '',
        exclude_custcategory: '',
        status: 'Delivered',
        person_active: [],
        is_commission: '0',
        is_gst_inclusive: '',
        gst_rate: '',
        item_id: [],
        account_manager: '',
        product_id: '',
        item_name: '',
        zone_id: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    // init page load
    // getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_custdetail').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Details (Sales)" + now + ".xls");
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
        $scope.search.edited = true;
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    $scope.onExACategoryChanged = function () {
        if ($scope.search.exACategory) {
            $scope.search.custcategory.push("2");
            $scope.search.exclude_custcategory = true;
        } else {
            $scope.search.custcategory.splice($scope.search.custcategory.indexOf("2"), 1);
            $scope.search.exclude_custcategory = false;
        }
        $scope.searchDB();
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/custdetail?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.transactions.data) {
                $scope.alldata = data.transactions.data;
                $scope.totalCount = data.transactions.total;
                $scope.currentPage = data.transactions.current_page;
                $scope.indexFrom = data.transactions.from;
                $scope.indexTo = data.transactions.to;
            } else {
                $scope.alldata = data.transactions;
                $scope.totalCount = data.transactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.transactions.length;
            }
            $scope.totals = data.totals;
            $scope.All = data.transactions.length;
            $scope.spinner = false;
        });
    }
}

function custSummaryController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.custcategories = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        edited: false,
        profile_id: '',
        current_month: moment().month() + 1 + '-' + moment().year(),
        id_prefix: '',
        custcategory: [],
        exACategory: '',
        exclude_custcategory: '',
        status: 'Delivered',
        person_active: [],
        is_commission: '0',
        is_gst_inclusive: '',
        gst_rate: '',
        item_id: '',
        zone_id: '',
        cust_id: '',
        company: '',
        pageNum: 100,
        sortBy: true,
        sortName: '',
    }
    // init page load
    // getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });
    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_custsummary').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Cust Summary (Sales)" + now + ".xls");
    };

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function () {
        if ($.isEmptyObject($scope.datasetTemp)) {
            $scope.datasetTemp = {
                pageNum: $scope.itemsPerPage
            }
        } else {
            $scope.datasetTemp['pageNum'] = $scope.itemsPerPage;
        }
        getPage(1, false);
    };

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1, false);
    }

    // when hitting search button
    // $scope.searchDB = function () {
    //     $scope.search.sortName = '';
    //     $scope.search.sortBy = true;
    //     getPage(1, false);
    // }
    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.edited = true;
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    $scope.onExACategoryChanged = function () {
        if ($scope.search.exACategory) {
            $scope.search.custcategory.push("2");
            $scope.search.exclude_custcategory = true;
        } else {
            $scope.search.custcategory.splice($scope.search.custcategory.indexOf("2"), 1);
            $scope.search.exclude_custcategory = false;
        }
        $scope.searchDB();
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/custsummary?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.transactions.data) {
                $scope.alldata = data.transactions.data;
                $scope.totalCount = data.transactions.total;
                $scope.currentPage = data.transactions.current_page;
                $scope.indexFrom = data.transactions.from;
                $scope.indexTo = data.transactions.to;
            } else {
                $scope.alldata = data.transactions;
                $scope.totalCount = data.transactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.transactions.length;
            }
            // get total count
            $scope.All = data.transactions.length;
            $scope.totals = data.totals;
            $scope.spinner = false;
        });
    }
}

function custSummaryGroupController($scope, $http) {
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
        edited: false,
        profile_id: '',
        current_month: moment().month() + 1 + '-' + moment().year(),
        id_prefix: '',
        custcategory: [],
        exACategory: '',
        exclude_custcategory: '',
        status: 'Delivered',
        person_active: [],
        is_commission: '0',
        is_gst_inclusive: '',
        gst_rate: '',
        item_id: '',
        zone_id: '',
        cust_id: '',
        company: '',
        pageNum: 100,
        sortBy: true,
        sortName: '',
    }
    // init page load
    // getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });
    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_custsummary_group').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Cust Summary (Group)" + now + ".xls");
    };

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function () {
        if ($.isEmptyObject($scope.datasetTemp)) {
            $scope.datasetTemp = {
                pageNum: $scope.itemsPerPage
            }
        } else {
            $scope.datasetTemp['pageNum'] = $scope.itemsPerPage;
        }
        getPage(1, false);
    };

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.edited = true;
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    $scope.onExACategoryChanged = function () {
        if ($scope.search.exACategory) {
            $scope.search.custcategory.push("2");
            $scope.search.exclude_custcategory = true;
        } else {
            $scope.search.custcategory.splice($scope.search.custcategory.indexOf("2"), 1);
            $scope.search.exclude_custcategory = false;
        }
        $scope.searchDB();
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/custsummary-group?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.transactions.data) {
                $scope.alldata = data.transactions.data;
                $scope.totalCount = data.transactions.total;
                $scope.currentPage = data.transactions.current_page;
                $scope.indexFrom = data.transactions.from;
                $scope.indexTo = data.transactions.to;
            } else {
                $scope.alldata = data.transactions;
                $scope.totalCount = data.transactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.transactions.length;
            }
            // get total count
            $scope.All = data.transactions.length;
            $scope.totals = data.totals;
            $scope.spinner = false;
        });
    }
}

function productMonthDetailController($scope, $http) {
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
        edited: false,
        profile_id: '',
        current_month: moment().month() + 1 + '-' + moment().year(),
        product_id: '',
        product_name: '',
        custcategory: [],
        exclude_custcategory: '',
        status: 'Delivered',
        person_active: [],
        is_commission: '0',
        item_id: '',
        zone_id: '',
        account_manager: '',
        cust_id: '',
        strictCustId: '',
        pageNum: 'All',
        sortBy: true,
        sortName: ''
    }
    // init page load
    // getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_productmonth').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Product Detail(Month)" + now + ".xls");
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
        $scope.search.edited = true;
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/productmonth?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
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
            $scope.All = data.items.length;
            $scope.total_amount = data.total_amount;
            $scope.total_qty = data.total_qty.toFixed(4);
            $scope.spinner = false;
        });
    }
}

function monthlyReportController($scope, $http) {
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
        edited: false,
        profile_id: '',
        current_year: moment().format('YYYY'),
        id_prefix: '',
        cust_id: '',
        company: '',
        custcategory: [],
        exclude_custcategory: '',
        status: 'Delivered',
        person_active: [],
        is_commission: '0',
        is_gst_inclusive: '',
        gst_rate: '',
        account_manager: '',
        item_id: '',
        product_id: '',
        item_name: '',
        item_id: '',
        zone_id: '',
        account_manager: '',
        exACategory: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    // init page load
    // getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_monthlyreport').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Monthly Report" + now + ".xls");
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
        $scope.search.edited = true;
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    $scope.onExACategoryChanged = function () {
        if ($scope.search.exACategory) {
            $scope.search.custcategory.push("2");
            $scope.search.exclude_custcategory = true;
        } else {
            $scope.search.custcategory.splice($scope.search.custcategory.indexOf("2"), 1);
            $scope.search.exclude_custcategory = false;
        }
        $scope.searchDB();
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $scope.search.current_year;
        $http.post('/api/detailrpt/sales/monthly-report?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.transactions.data) {
                $scope.alldata = data.transactions.data;
                $scope.totalCount = data.transactions.total;
                $scope.currentPage = data.transactions.current_page;
                $scope.indexFrom = data.transactions.from;
                $scope.indexTo = data.transactions.to;
            } else {
                $scope.alldata = data.transactions;
                $scope.totalCount = data.transactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.transactions.length;
            }
            $scope.months = data.months;
            $scope.All = data.transactions.length;
            $scope.spinner = false;
        });
    }
}

function productDayDetailController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        edited: false,
        cust_id: '',
        delivery_from: $scope.today,
        product_id: '',
        company: '',
        delivery_to: $scope.today,
        product_name: '',
        profile_id: '',
        custcategory: [],
        exACategory: '',
        exclude_custcategory: '',
        person_active: [],
        // status: 'Confirmed',
        statuses: ['Confirmed'],
        is_commission: '0',
        is_inventory: '1',
        driver: [],
        zone_id: '',
        account_manager: '',
        item_id: [],
        pageNum: 'All',
        sortBy: true,
        sortName: ''
    }
    // init page load
    // getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.onDeliveryFromChanged = function (date) {
        if (date) {
            $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onDeliveryToChanged = function (date) {
        if (date) {
            $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onPrevSingleClicked = function (scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextSingleClicked = function (scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_productday').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Product Detail(Day)" + now + ".xls");
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
        $scope.search.edited = true;
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1);
        $scope.search.edited = false;
    }

    $scope.driverInit = function (driver) {
        $scope.search.driver = [driver];
        $scope.search.status = 'Confirmed';
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    $scope.onExACategoryChanged = function () {
        if ($scope.search.exACategory) {
            $scope.search.custcategory.push("2");
            $scope.search.exclude_custcategory = true;
        } else {
            $scope.search.custcategory.splice($scope.search.custcategory.indexOf("2"), 1);
            $scope.search.exclude_custcategory = false;
        }
        $scope.searchDB();
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/productday?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
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
            $scope.All = data.items.length;
            $scope.total_amount = data.total_amount;
            $scope.total_qty = data.total_qty.toFixed(4);
            $scope.spinner = false;
        });
    }
}

function invoiceBreakdownController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.today = moment().format("YYYY-MM-DD");
    // $scope.monthstart = moment().startOf('month').format('YYYY-MM-DD');
    $scope.monthend = moment().endOf('month').format('YYYY-MM-DD');
    $scope.search = {
        person_id: '1286',
        status: 'Delivered',
        // delivery_from: $scope.monthstart,
        delivery_from: '2017-03-01',
        delivery_to: $scope.monthend,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2({ placeholder: 'Select...' });
    });

    $scope.onDeliveryFromChanged = function (date) {
        if (date) {
            $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onDeliveryToChanged = function (date) {
        if (date) {
            $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_invoicebreakdown').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Invoice Breakdown" + now + ".xls");
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
        $scope.search.sortBy = '';
        getPage(1, false);
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/detailrpt/sales/invbreakdown', $scope.search).success(function (data) {
            $scope.transactions = data;
            $scope.spinner = false;
        });
    }
}

app.filter('delDate', [
    '$filter', function ($filter) {
        return function (input, format) {
            if (input) {
                return $filter('date')(new Date(input), format);
            } else {
                return '';
            }
        };
    }
]);

app.config(function ($provide) {
    $provide.decorator('mFormatFilter', function () {
        return function newFilter(m, format, tz) {
            if (!(moment.isMoment(m))) {
                return '';
            }
            return tz ? moment.tz(m, tz).format(format) : m.format(format);
        };
    });
});

app.controller('custDetailController', custDetailController);
app.controller('custSummaryController', custSummaryController);
app.controller('custSummaryGroupController', custSummaryGroupController);
app.controller('productMonthDetailController', productMonthDetailController);
app.controller('monthlyReportController', monthlyReportController);
app.controller('productDayDetailController', productDayDetailController);
app.controller('invoiceBreakdownController', invoiceBreakdownController);