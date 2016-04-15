var app = angular.module('app', ['ui.bootstrap',
                                'ui.bootstrap.datetimepicker']);

    function invController($scope, $http){

        $scope.typeModel = 'Incoming';

        $scope.showBatch = true;

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

        $scope.typeModelChanged = function(type){

            if(type == 'Adjustment'){

                $scope.showBatch = false;

            }else{

                $scope.showBatch = true;
            }

        }

    }


app.controller('invController', invController);

