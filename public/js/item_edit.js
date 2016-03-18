var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function itemController($scope, $q, $http){

    var item_id = $('#item_id').val();
    var img_count = $('#img_remain').val();
    $scope.currentPage = 1;
    $scope.itemsPerPage = 30;

    $http.get('/item/image/' + item_id).success(function(images){
        $scope.images = images;
        $scope.imageLength = images.length;
        $scope.getCaptionInit = function(image_id){
            var caption = '';
            for(var i = 0; i < $scope.images.length; i ++){
                var image = $scope.images[i];
                if(image_id == image.id){
                    caption = image.caption;
                    return caption;
                }
            }
        }
    });

    Dropzone.autoDiscover = false;
    $('.dropzone').dropzone({
        maxFiles: img_count,
        init: function()
        {
            this.on("complete", function()
            {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                location.reload();
              }
            }),
            this.on("maxfilesexceeded", function(file){
                alert("Reach the maximum upload amount!");
            });
        }
    });

        //delete record
        $scope.confirmDelete = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/item/image/' + id
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

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('itemController', itemController);
app.controller('repeatController', repeatController);
