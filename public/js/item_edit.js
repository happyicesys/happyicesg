var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

function itemController($scope, $q, $http) {

    var item_id = $('#item_id').val();
    var img_count = $('#img_remain').val();
    $scope.currentPage = 1;
    $scope.itemsPerPage = 30;
    $scope.baseItemUom = '';

    function getDefaultFormUom() {
        return {
            id: '',
            name: '',
            value: '',
            tempValue: '',
            is_base_unit: false,
            is_transacted_unit: false,
        }
    }

    getUoms();

    $http.get('/item/image/' + item_id).success(function (images) {
        $scope.images = images;
        $scope.imageLength = images.length;
        $scope.getCaptionInit = function (image_id) {
            var caption = '';
            for (var i = 0; i < $scope.images.length; i++) {
                var image = $scope.images[i];
                if (image_id == image.id) {
                    caption = image.caption;
                    return caption;
                }
            }
        }
    });

    function getUoms() {
        $http.get('/api/item/' + item_id + '/item-uom').success(function (data) {
            $scope.itemUoms = data.itemUoms;
            $scope.baseItemUom = data.baseItemUom;
        });
    }

    Dropzone.autoDiscover = false;
    $('.dropzone').dropzone({
        maxFiles: img_count,
        init: function () {
            this.on("complete", function () {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    location.reload();
                }
            }),
                this.on("maxfilesexceeded", function (file) {
                    alert("Reach the maximum upload amount!");
                });
        }
    });

    //delete record
    $scope.confirmDelete = function (id) {
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/item/image/' + id
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

    $scope.onItemUomCreateClicked = function (event) {
        event.preventDefault();
        $scope.formUom = getDefaultFormUom();
    }

    $scope.onIsBaseUnitChecked = function () {
        if ($scope.formUom.is_base_unit) {
            $scope.formUom.tempValue = $scope.formUom.value;
            $scope.formUom.value = 1;
        } else {
            if ($scope.formUom.tempValue) {
                $scope.formUom.value = $scope.formUom.tempValue;
            }
        }
    }

    $scope.onFormUomSaveClicked = function (event) {
        event.preventDefault();
        $http.post('/api/item/' + item_id + '/uom/create-update', $scope.formUom).success(function (data) {
            getUoms();
        });
    }

    $scope.onItemUomEditClicked = function (data) {
        $scope.formUom = data;
    }

    $scope.onItemUomDeleteClicked = function (data) {
        var isConfirmDelete = confirm('Are you sure you want delete this uom? (smallest value will become base uom if there isnt any)');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: '/api/item/item-uom/' + data.id
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

function repeatController($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('itemController', itemController);
app.controller('repeatController', repeatController);
