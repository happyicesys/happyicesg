var app = angular.module('app', ['ui.bootstrap',
                                'ui.bootstrap.datetimepicker']);

    function invController($scope, $http){

        $scope.typeModel = 'Incoming';

        $scope.showBatch = true;

        var inv_id = $('#inventory_id').val();
/*
        $http.get('/item/data').success(function(items){
            $scope.items = items;

            $http.get('/inventory/item/'+ inv_id).success(function(inventories){

                $scope.inventories = inventories;
                $scope.getRetailInit = function(item_id){
                    var retailNum = 0;
                    for(var i = 0; i < $scope.inventories.length; i ++){
                        var inventory = $scope.inventories[i];
                        if(item_id == inventory.item_id){
                            retailNum = inventory.retail_price;
                            return retailNum;
                        }
                    }
                }

                $scope.getQuoteInit = function(item_id){
                    var quoteNum = 0;
                    for(var i = 0; i < $scope.inventories.length; i ++){
                        var inventory = $scope.inventories[i];
                        if(item_id == inventory.item_id){
                            quoteNum = inventory.quote_price;
                            return quoteNum;
                        }
                    }
                }
            });
        });*/


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

        $scope.typeModelChanged = function(type){

            if(type == 'Adjustment'){

                $scope.showBatch = false;

            }else{

                $scope.showBatch = true;
            }

        }



    }


app.controller('invController', invController);

