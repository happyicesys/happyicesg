var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize']);

    function personEditController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;  

        angular.element(document).ready(function () {

            $http.get('/person/transac/'+ $('#person_id').val()).success(function(transactions){
                $scope.transactions = transactions;       
            });            

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete the entry');
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/transaction/data/' + id
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

           function multInputs() {
            "use strict";
               var mult = 0;
               // for each row:
               $("tr.txtMult").each(function () {
                   // get the values from this row:
                   var $qty = eval($('.qtyClass', this).val()) * 1;

                   var $quote = ($('.quoteClass', this).val()) * 1;

                   var $retail = ($('.retailClass', this).val()) * 1;

                   var $price = 0;

                   if($quote == null || $quote == '' || $quote == 0){

                        $price = $retail;

                   }else{

                        $price = $quote;

                   }

                   var $total = ($qty * $price).toFixed(2);
                   // set total for the row
                   // $('.amountClass', this).text($total);
                    if(isNaN($total)) {
                        var $total = 0;
                    }                   
                   $('.amountClass', this).val($total);
                   mult += parseFloat($total);
               });

               $('.grandTotal').val(mult.toFixed(2));
           }            
        });
    }  

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    

app.controller('personEditController', personEditController);
app.controller('repeatController', repeatController);
