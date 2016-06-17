var app = angular.module('app', []);

    $('.select').select2();

    function d2dorderController($scope, $http){
console.log('sohai');
        $scope.onItemChanged = function(itemModel){

            console.log(itemModel);
        }

    }

app.controller('d2dorderController', d2dorderController);
