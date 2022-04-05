var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
]);

function priceTemplateController($scope, $http) {
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
        priceTemplates: [],
        pageNum: 100,
        sortBy: true,
        sortName: ''
    }
    $scope.form = getDefaultForm()
    // init page load
    getPage();

    function getDefaultForm() {
        return {
            id: '',
            name: '',
            desc: '',
            files: [],
            item: '',
            sequence: '',
            price_template_items: []
        }
    }

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
        var blob = new Blob(["\ufeff", document.getElementById('exportable_price_template').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Price Template" + now + ".xls");
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

    $scope.onPriceTemplateCreateClicked = function () {
        $scope.form = getDefaultForm();
    }

    $scope.onPriceTemplateDelete = function (data) {
        var isConfirmDelete = confirm('Are you sure you want to delete the price template & detach its binding(s)?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/api/price-template/delete/' + data.id
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

    $scope.onPriceTemplatePersonUnbind = function (id) {
        $http({
            method: 'POST',
            url: '/api/price-template/person/' + id + '/unbind'
        })
            .success(function (data) {
                getPage(1, false);
            })
            .error(function (data) {
                alert('Unable to delete');
            })
    }

    // create
    $scope.onAddPriceTemplateItemClicked = function () {
        const item = JSON.parse($scope.form.item);
        const sequence = $scope.form.sequence;
        const retail_price = $scope.form.retail_price;
        const quote_price = $scope.form.quote_price;
        $scope.form.price_template_items.push({
            item: item,
            sequence: sequence,
            retail_price: retail_price.toFixed(2),
            quote_price: quote_price.toFixed(2),
        });
        $scope.form.sequence = ''
        $scope.form.retail_price = ''
        $scope.form.quote_price = ''
    }

    // single edit entry clicked
    $scope.onSinglePriceTemplateClicked = function (data) {
        $scope.form = getDefaultForm()
        $('.select').select2({
            placeholder: 'Select...'
        });
        $scope.form = data
    }

    // delete single entry api
    $scope.onSingleEntryDeleted = function (item) {
        let index = $scope.form.price_template_items.indexOf(item);
        $scope.form.price_template_items.splice(index, 1)
    }

    // bind
    $scope.onPriceTemlatePersonBindingClicked = function () {
        $http.post('/api/price-template/person/bind', $scope.form).success(function (data) {
            getPage(1, false);
        });
    }

    // upon form submit
    $scope.onFormSubmitClicked = function () {
        $http.post('/api/price-template/store-update', $scope.form).success(function (data) {
            $scope.form = getDefaultForm()
            $('.select').select2({
                placeholder: 'Select...'
            });
            getPage(1)
        });
    }

    $scope.errors = [];
    $scope.attachments = [];
    var formData = new FormData();

    $scope.setTheFile = function ($attachments) {
        angular.forEach($attachments, function (value, key) {
            formData.append('attachments', value);
        });
    };

    $scope.uploadFile = function (event) {
        event.preventDefault();
        var request = {
            method: 'POST',
            url: '/api/price-template/attachment',
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.attachments = e.data.attachments;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#price_template_attachment');
                fileElement.value = '';
                if (e.data === 'true') {
                    alert("Attachment uploaded");
                } else {
                    alert("Upload failure");
                }
                $scope.searchDB();
            }, function error(e) {
                $scope.errors = e.data.errors;
                alert('Upload failure, please try again')
            });
    };

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/price-template?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.priceTemplates.data) {
                $scope.alldata = data.priceTemplates.data;
                $scope.totalCount = data.priceTemplates.total;
                $scope.currentPage = data.priceTemplates.current_page;
                $scope.indexFrom = data.priceTemplates.from;
                $scope.indexTo = data.priceTemplates.to;
            } else {
                $scope.alldata = data.priceTemplates;
                $scope.totalCount = data.priceTemplates.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.priceTemplates.length;
            }
            $scope.spinner = false;
        });
    }

}

app.controller('priceTemplateController', priceTemplateController);
