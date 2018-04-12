var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'ae-datetimepicker'
                                ]);

function analogDifferenceController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        cust_id: '',
        company: '',
        pageNum: 100,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Rpt"+ now + ".xls");
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

      // when hitting search button
    $scope.searchDB = function(){
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve franchisee id
    $scope.getFranchiseeId = function() {
        $http.get('/api/franchisee/auth').success(function(data) {
            return data;
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/franchisee/people?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.people.data){
                $scope.alldata = data.people.data;
                $scope.totalCount = data.people.total;
                $scope.currentPage = data.people.current_page;
                $scope.indexFrom = data.people.from;
                $scope.indexTo = data.people.to;
            }else{
                $scope.alldata = data.people;
                $scope.totalCount = data.people.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.people.length;
            }
            // get total count
            $scope.All = data.people.length;

            // return total amount
            $scope.spinner = false;
        });
    }
}

function invoiceSummaryController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.monthstart = moment().startOf('month').format('YYYY-MM-DD');
    $scope.monthend = moment().endOf('month').format('YYYY-MM-DD');
    $scope.search = {
        collection_from: $scope.monthstart,
        collection_to: $scope.today,
        cust_id: '',
        company: '',
        person_id: '',
        is_active: 'Yes',
        pageNum: 100,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_invoicesummary').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Invoice Summary" + now + ".xls");
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

    // when hitting search button
    $scope.searchDB = function () {
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve franchisee id
    $scope.getFranchiseeId = function () {
        $http.get('/api/franchisee/auth').success(function (data) {
            return data;
        });
    }

    $scope.onCollectionFromChanged = function (date) {
        if(date) {
            $scope.search.collection_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onCollectionToChanged = function (date) {
        if(date) {
            $scope.search.collection_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onPrevDateClicked = function () {
        $scope.search.collection_from = moment(new Date($scope.search.collection_from)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.search.collection_to = moment(new Date($scope.search.collection_to)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onTodayDateClicked = function () {
        $scope.search.collection_from = moment().format('YYYY-MM-DD');
        $scope.search.collection_to = moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextDateClicked = function () {
        $scope.search.collection_from = moment(new Date($scope.search.collection_from)).add(1, 'days').format('YYYY-MM-DD');
        $scope.search.collection_to = moment(new Date($scope.search.collection_to)).add(1, 'days').format('YYYY-MM-DD');
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
    

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/invsummaries?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.ftransactions.data) {
                $scope.alldata = data.ftransactions.data;
                $scope.totalCount = data.ftransactions.total;
                $scope.currentPage = data.ftransactions.current_page;
                $scope.indexFrom = data.ftransactions.from;
                $scope.indexTo = data.ftransactions.to;
            } else {
                $scope.alldata = data.ftransactions;
                $scope.totalCount = data.ftransactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.ftransactions.length;
            }
            // get total count
            $scope.All = data.ftransactions.length;

            $scope.avg_grand_total = data.dynamictotals.avg_grand_total.toFixed(2);
            $scope.avg_finaltotal = data.dynamictotals.avg_finaltotal.toFixed(2);
            $scope.avg_cost = data.dynamictotals.avg_cost.toFixed(2);
            $scope.avg_gross_profit = data.dynamictotals.avg_gross_profit.toFixed(2);
            $scope.avg_gross_profit_percent = data.dynamictotals.avg_gross_profit_percent.toFixed(2);
            $scope.avg_sales_qty = data.dynamictotals.avg_sales_qty.toFixed(2);
            $scope.avg_sales_avg_day = data.dynamictotals.avg_sales_avg_day.toFixed(2);
            $scope.avg_delta = data.dynamictotals.avg_delta.toFixed(2);
            $scope.avg_stock_in = data.dynamictotals.avg_stock_in.toFixed(2);

            $scope.total_grand_total = data.dynamictotals.total_grand_total.toFixed(2);
            $scope.total_finaltotal = data.dynamictotals.total_finaltotal.toFixed(2);
            $scope.total_taxtotal = data.dynamictotals.total_taxtotal.toFixed(2);
            $scope.total_cost = data.dynamictotals.total_cost.toFixed(2);
            $scope.total_gross_profit = data.dynamictotals.total_gross_profit.toFixed(2);
            $scope.total_gross_profit_percent = data.dynamictotals.total_gross_profit_percent.toFixed(2);
            $scope.total_owe = data.dynamictotals.total_owe.toFixed(2);
            $scope.total_paid = data.dynamictotals.total_paid.toFixed(2);
            $scope.total_sales_qty = data.dynamictotals.total_sales_qty.toFixed(2);
            $scope.total_delta = data.dynamictotals.total_delta.toFixed(2);
            $scope.total_stock_in = data.dynamictotals.total_stock_in.toFixed(2);
            // return total amount
            $scope.spinner = false;
        });
    }
}

function varianceManagementController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.form = {

    };
    $scope.search = {
        datein_from: $scope.today,
        datein_to: $scope.today,
        cust_id: '',
        company: '',
        pageNum: 100,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Rpt"+ now + ".xls");
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

      // when hitting search button
    $scope.searchDB = function(){
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // retrieve franchisee id
    $scope.getFranchiseeId = function() {
        $http.get('/api/franchisee/auth').success(function(data) {
            return data;
        });
    }

    $scope.onPrevDateClicked = function() {
        $scope.search.datein_from = moment(new Date($scope.search.datein_from)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.search.datein_to = moment(new Date($scope.search.datein_to)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onTodayDateClicked = function() {
        $scope.search.datein_from = moment().format('YYYY-MM-DD');
        $scope.search.datein_to = moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextDateClicked = function() {
        $scope.search.datein_from = moment(new Date($scope.search.datein_from)).add(1, 'days').format('YYYY-MM-DD');
        $scope.search.datein_to = moment(new Date($scope.search.datein_to)).add(1, 'days').format('YYYY-MM-DD');
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

    $scope.isFormValid = function() {
        let invalid = true;
        if($scope.form.person_id && $scope.form.datein && $scope.form.pieces && $scope.form.reason) {
            invalid = false;
        }
        return invalid;
    }

    $scope.addEntry = function() {
        let inputData = {
            'person_id': $scope.form.person_id,
            'datein': moment(new Date($scope.form.datein)).format('YYYY-MM-DD'),
            'pieces': $scope.form.pieces,
            'reason': $scope.form.reason,
        }
        $http.post('/api/variances/submitEntry', inputData). success(function(data) {
            getPage(1);

            $scope.form = {
                datein: '',
                pieces: '',
                reason: '',
            }

            $('.select').val('').trigger('change')
        }).error(function(data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function(id) {
        $http.delete('/api/variances/' + id + '/delete').success(function(data) {
            getPage(1);
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/variances?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.variances.data){
                $scope.alldata = data.variances.data;
                $scope.totalCount = data.variances.total;
                $scope.currentPage = data.variances.current_page;
                $scope.indexFrom = data.variances.from;
                $scope.indexTo = data.variances.to;
            }else{
                $scope.alldata = data.variances;
                $scope.totalCount = data.variances.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.variances.length;
            }
            // get total count
            $scope.All = data.variances.length;

            // return total amount
            $scope.spinner = false;
        });
    }
}
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

app.controller('analogDifferenceController', analogDifferenceController);
app.controller('varianceManagementController', varianceManagementController);
app.controller('invoiceSummaryController', invoiceSummaryController);