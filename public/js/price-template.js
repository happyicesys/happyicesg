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
        person_id: [],
        active: ['Yes'],
        priceTemplates: [],
        pageNum: 100,
        sortBy: true,
        sortName: ''
    }
    $scope.form = getDefaultForm()
    $scope.uoms = [];
    // init page load
    getUoms();
    getPage();

    function getDefaultForm() {
        return {
            id: '',
            name: '',
            desc: '',
            files: [],
            item: '',
            sequence: '',
            price_template_items: [],
            price_template_item_uoms: [],
        }
    }

    function getUoms() {
        $http.get('/api/uoms').success(function (data) {
            $scope.uoms = data;
        });
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
            retail_price: retail_price ? retail_price.toFixed(2) : 0,
            quote_price: quote_price ? quote_price.toFixed(2) : 0,

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

        // let result = angular.forEach(data.price_template_items, function(price_template_item, index) {
        //     if (price_template_item.price_template_item_uoms.length) {
        //         angular.forEach(price_template_item.price_template_item_uoms, function (price_template_item_uom, index) {
        //             if (price_template_item_uom.item_uom_id == itemUomId) {
        //                 price_template_item.item.checked[]
        //             }
        //         })
        //     }
        // })

        // $scope.checkExistPriceTemplateItemUom = function (itemUomId, priceTemplateItem) {
        //     let result = false;
        //     let priceTemplateItemUomId = '';
        //     if (priceTemplateItem.price_template_item_uoms.length) {
        //         angular.forEach(priceTemplateItem.price_template_item_uoms, function (value, index) {
        //             if (value.item_uom_id == itemUomId) {
        //                 result = true;
        //             }
        //         })
        //     }
        //     return {
        //         'result': result,
        //         'priceTemplateItemUomId': priceTemplateItemUomId,
        //     };
        // }

        $scope.form = data
    }

    $scope.checkExistPriceTemplateItemUom = function (itemUomId, priceTemplateItem) {
        let result = false;
        let priceTemplateItemUomId = '';
        if (priceTemplateItem.price_template_item_uoms.length) {
            angular.forEach(priceTemplateItem.price_template_item_uoms, function (value, index) {
                if (value.item_uom_id == itemUomId) {
                    result = true;
                }
            })
        }
        return {
            'result': result,
            'priceTemplateItemUomId': priceTemplateItemUomId,
        };
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
        console.log($scope.checked)
        $http.post('/api/price-template/store-update', $scope.form).success(function (data) {
            $scope.form = getDefaultForm()

            $('.select').select2({
                placeholder: 'Select...'
            });
            getPage(1)
        });
    }

    // $scope.errors = [];
    // $scope.attachments = [];
    // var formData = new FormData();

    // $scope.setTheFile = function ($attachments) {
    //     angular.forEach($attachments, function (value, key) {
    //         formData.append('attachments', value);
    //     });
    // };

    // $scope.uploadFile = function (event) {
    //     event.preventDefault();
    //     var request = {
    //         method: 'POST',
    //         url: '/api/price-template/attachment',
    //         data: formData,
    //         headers: {
    //             'Content-Type': undefined
    //         }
    //     };
    //     $http(request)
    //         .then(function success(e) {
    //             $scope.attachments = e.data.attachments;
    //             $scope.errors = [];
    //             // clear uploaded file
    //             var fileElement = angular.element('#price_template_attachment');
    //             fileElement.value = '';
    //             if (e.data === 'true') {
    //                 alert("Attachment uploaded");
    //             } else {
    //                 alert("Upload failure");
    //             }
    //             $scope.searchDB();
    //         }, function error(e) {
    //             $scope.errors = e.data.errors;
    //             alert('Upload failure, please try again')
    //         });
    // };

    $scope.onReplicatePriceTemplateClicked = function (data) {
        $http.post('/api/price-template/replicate', { id: data.id }).success(function (data) {
            $scope.form = getDefaultForm()
            $('.select').select2({
                placeholder: 'Select...'
            });
            getPage(1)
            $('#price-template-modal').modal('hide');
        });
    }

    $scope.onSortSequenceClicked = function (event) {
        event.preventDefault();
        $http.post('/api/price-template/sort-sequence', { form: $scope.form }).success(function (data) {
            $scope.form = data;
        });
    }

    $scope.onRenumberSequenceClicked = function (event) {
        event.preventDefault();
        let isConfirm = confirm('Are you sure to generate sequence based on this arrangement?')
        if (isConfirm) {
            $http.post('/api/price-template/renumber-sequence', { form: $scope.form }).success(function (data) {
                $scope.form = data;
            });
        }
    }

    $scope.onImageClicked = function (id, data = null) {
        if (!$scope.form.id) {
            $scope.onSingleEntryEdit(data)
        }
        getImagePage(1, id)
    }

    // removing file
    $scope.removeFile = function (attachmentId) {
        $http.post('/api/price-template/attachment/delete', { 'attachmentId': attachmentId }).success(function (data) {
            getPage(1);
            location.reload();
        });
    }

    $scope.errors = [];
    $scope.files = [];
    var formData = new FormData();

    // $scope.uploadFile = function (priceTemplateId) {
    //     var request = {
    //         method: 'POST',
    //         url: '/api/price-template/' + priceTemplateId + '/attachment',
    //         data: formData,
    //         headers: {
    //             'Content-Type': undefined
    //         }
    //     };
    //     $http(request)
    //         .then(function success(e) {
    //             $scope.files = e.data.files;
    //             $scope.errors = [];
    //             // clear uploaded file
    //             var fileElement = angular.element('#image_file');
    //             fileElement.value = '';
    //             getPage(1, false)
    //             alert("Image has been uploaded successfully!");
    //         }, function error(e) {
    //             $scope.errors = e.data.errors;
    //         });
    // };

    $scope.uploadFile = function (event, priceTemplateId) {
        event.preventDefault();
        var request = {
            method: 'POST',
            url: '/api/price-template/' + priceTemplateId + '/attachment',
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#image_file');
                fileElement.value = '';
                if (e.data === 'true') {
                    alert("Upload Successful");
                }
                $scope.searchDB();
            }, function error(e) {
                $scope.errors = e.data.errors;
                alert('Upload unsuccessful, please try again')
            });
    };

    $scope.setTheFiles = function ($files) {
        angular.forEach($files, function (value, key) {
            //   console.log(value)
            formData.append('image_file', value);
        });
    };

    $scope.onPriceTemplateItemUomChanged = function (priceTemplateItem, itemUom) {
        $http.post('/api/price-template-item/' + priceTemplateItem.id + '/item-uom/' + itemUom.id + '/toggle').success(function (data) {
            // getPage(1, false);
        });
    }

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

app.directive('ngFiles', ['$parse', function ($parse) {

    function file_links(scope, element, attrs) {
        var onChange = $parse(attrs.ngFiles);
        element.on('change', function (event) {
            onChange(scope, { $files: event.target.files });
        });
    }
    return {
        link: file_links
    }
}]);

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

app.controller('priceTemplateController', priceTemplateController);
