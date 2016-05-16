var app = angular.module('app', ['ui.bootstrap',
                                'ui.bootstrap.datetimepicker']);

function invController($scope, $http){

    $scope.originalModel = 0;

    var inv_id = $('#inventory_id').val();

    $http.get('/inventory/item/'+ inv_id).success(function(inventories){
        $scope.inventories = inventories;

        $scope.getOriginalInit = function(item_id){
            var num = 0;
            for(var i = 0; i < $scope.inventories.length; i ++){
                var inventory = $scope.inventories[i];
                if(item_id == inventory.item.id){
                    num = inventory.qtyrec_current;
                    return num ? num : 0;
                }
            }
        }

        $scope.getIncomingInit = function(item_id){
            var num = 0;
            for(var i = 0; i < $scope.inventories.length; i ++){
                var inventory = $scope.inventories[i];
                if(item_id == inventory.item.id){
                    num = inventory.qtyrec_incoming;
                    return num;
                }
            }
        }

        $scope.compareModel = function(item_id, originalModel, incomingModel){
            var num = 0;
            for(var i = 0; i < $scope.inventories.length; i ++){
                var inventory = $scope.inventories[i];
                if(item_id == inventory.item.id){
                    num = inventory.qtyrec_incoming;
                }
            }

            if(originalModel == incomingModel || num == incomingModel){

                return false;

            }else{

                return true;
            }
        }
    });

    $http.get('/item/data').success(function(items){
        $scope.items = items;
    });

    $(document).ready(function () {

        $(".incomingClass").keyup(multInputs);

        function multInputs() {
            "use strict";
            var currentTotal = 0;
            var incomingTotal = 0;
            var afterTotal = 0;
            // for each row:
            $("tr.txtMult").each(function () {
                // get the values from this row:
                var $current = (+$('.currentClass', this).val());

                var $incoming = eval($('.incomingClass', this).val());

                if($incoming == 0 || $incoming == null){

                    var $after = $current;

                }else{

                    var $after = $current + $incoming;

                }

                if(isNaN($after)) {

                    var $after = 0;

                }

                if($incoming === undefined){

                    $incoming = 0;
                }

                $('.afterClass', this).val($after.toFixed(4));

                currentTotal += (+$current);
                incomingTotal += ($incoming);
                afterTotal += (+$after);
            });

            $('.currentTotal').val(currentTotal.toFixed(4));
            $('.incomingTotal').val(incomingTotal.toFixed(4));
            $('.afterTotal').val(afterTotal.toFixed(4));
        }
    });


    //delete item record
    $scope.confirmDelete = function(){
        var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + inv_id);
        if(isConfirmDelete){
            $http({
                method: 'DELETE',
                url: '/inventory/data/' + inv_id
            })
            .success(function(data){
                window.history.go(-1);
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

app.filter('sumByKey', function() {
    return function(data, key) {
        if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
            return 0;
        }

        var sum = 0;
        for (var i = data.length - 1; i >= 0; i--) {
            sum += parseFloat(data[i][key]);
            console.log(data[i][key]);
        }


        return sum;
    };
});


app.controller('invController', invController);

