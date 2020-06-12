var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
]);

function pricematrixController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.search = {
        cust_id: '',
        custcategory_id: '',
        company: '',
        product_id: '',
        name: '',
        is_inventory: '1',
        pageNum: 'All',
        sortBy: true,
        sortName: ''
    }
    $scope.form = {
        retail_price: '',
        quote_price: ''
    }
    $scope.spinner = false;
    // init page load


    angular.element(document).ready(function () {
        $('.select').select2();
    });

    // when hitting search button
    $scope.searchDB = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage();
    }

    $scope.onCostrateChanged = function (person) {
        $http.post('/api/pricematrix/costrate/edit', person).success(function (data) { });
    }

    $scope.onPriceChanged = function (price) {
        $http.post('/api/pricematrix/edit', price).success(function (data) {});
    }

    // on individual override button clicked
    $scope.onOverrideButtonClicked = function(retail_price, quote_price, itemindex) {
        console.log(retail_price);
        console.log(quote_price);
        console.log(itemindex);
        console.log($scope.items[itemindex]);
    }

    // checkbox all
    $scope.onCheckAllChecked = function() {
        var checked = $scope.form.checkall;

        $scope.people.forEach(function (transaction, key) {
            $scope.people[key].check = checked;
        });
    }

    // retrieve page w/wo search
    function getPage() {
        $scope.spinner = true;
        $http.post('/api/pricematrix', $scope.search).success(function (data) {
            $scope.people = data.people;
            $scope.items = data.items;
            $scope.prices = data.prices;

/*
            if (data.people.data) {
                $scope.dates = data.dates;
                $scope.alldata = data.alldata;
                $scope.people = data.people.data;
                $scope.totalCount = data.people.total;
                $scope.currentPage = data.people.current_page;
                $scope.indexFrom = data.people.from;
                $scope.indexTo = data.people.to;
            } else {
                $scope.dates = data.dates;
                $scope.people = data.people;
                $scope.alldata = data.alldata;
                $scope.totalCount = data.people.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.people.length;
            } */
            // get total count
            $scope.All = data.people.length;
            // return fixed total amount
            $scope.spinner = false;
        });
    }
}

app.controller('pricematrixController', pricematrixController);