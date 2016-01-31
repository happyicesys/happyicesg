var app = angular.module('app', [   'ui.bootstrap', 
                                    'angularUtils.directives.dirPagination',
                                    'ui.select', 
                                    'ngSanitize'
                                ]);

        var $person = $('.person');
        var $item = $('.item');
        var $amount = $('#amount');
        var $trans_id = $('#transaction_id'); 
        var $person_select = $('.person_select');

        $person.select2();
        $item.select2({
            placeholder: "Select Item...",
        });        

    function transactionController($scope, $http){

        $scope.selection = {};
        $scope.Math = window.Math;
           
            $(document).ready(function () {

               $(".qtyClass").keyup(multInputs);
               $(".quoteClass").keyup(multInputs);

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

            $http.get('/person/data').success(function(people){
            $scope.people = people;
            });

            $http({
                url: '/transaction/' + $trans_id.val(),
                method: "GET",
            }).success(function(transaction){

                $http({
                    url: '/deal/data/' + transaction.id,
                    method: "GET",
                }).success(function(deals){ 
                    $scope.deals = deals;

                    var total = 0;
                    for(var i = 0; i < $scope.deals.length; i++){
                        var deal = $scope.deals[i];
                        total += (deal.amount/100*100);
                    }

                        $http({
                            url: '/person/profile/' + transaction.person_id,
                            method: "GET",
                        }).success(function(profile){ 
    /*
                            if(profile.gst){

                                $scope.totalModel = (total * 107/100).toFixed(2);
                                console.log('gst'+ $scope.totalModel);

                            }else{*/

                                $scope.totalModel = total.toFixed(2);
                                    
                            // }
                            
                            $http.put('total', $scope.totalModel)
                                .success(function(){
                            });                                                

                        });

                $http({
                    url: '/transaction/person/'+ transaction.person_id,
                    method: "GET",
                }).success(function(person){
                    $scope.personModel = person.id;
                    $scope.nameModel = person.name;
                    $scope.billModel = person.bill_address;
                    $scope.delModel = person.del_address + ' ' + person.del_postcode;
                    $scope.paytermModel = person.payterm;
                    $scope.personcodeModel = person.cust_id;
                    $scope.contactModel = person.contact;
                    $scope.attNameModel = person.name;
                    if(transaction.transremark){
                        
                        $scope.transremarkModel = transaction.transremark;

                    }else{

                        $scope.transremarkModel = person.remark;    
                    }
                    
                    $('.date').datetimepicker({
                        format: 'DD MMM YY'
                    });

                        $http({
                            url: '/transaction/item/'+ person.id,
                            method: "GET",
                        }).success(function(items){
                            $scope.items = items;          
                        });            
                }); 

            });            

        });



    $scope.onPersonSelected = function (person){

        $http({
            url: '/transaction/person/'+ person,
            method: "GET",
        
        }).success(function(person){ 
            $scope.billModel = person.bill_address + ' ' + person.bill_postcode;
            $scope.delModel = person.del_address + ' ' + person.del_postcode;
            $scope.paytermModel = person.payterm;
            $scope.personcodeModel = person.cust_id;
            $('.date').datetimepicker({
            format: 'DD MMM YY'
            });
            $('.date').val('');

            $http({
                url: '/transaction/item/'+ person.id,
                method: "GET",
            
            }).success(function(items){
                console.log(items);
                $scope.items = items;             
                $scope.qtyModel = [];
                $scope.amountModel = [];
                $scope.unitModel = [];

                $http.put('editperson', $scope.personModel)
                            .success(function(){
                            });

                /*$http.put('editpersoncode', $scope.personModel)
                            .success(function(){
                            }); */
                $http({
                    url: '/transaction/' + $trans_id.val() + '/editpersoncode' ,
                    method: "POST",
                    data: {person_code: $scope.personcodeModel},
                    }).success(function(response){
                    });
                                                                      

                $scope.onItemSelected = function (item_id){

                    $http({
                        url: '/transaction/person/'+ person.id + '/item/' + item_id,
                        method: "GET",

                    }).success(function(prices){
                        $scope.prices = prices;
                        $scope.qtyModel = 1;
                        $scope.unitModel = prices.item.unit;
                        $scope.amountModel = prices.quote_price;

                        $scope.onQtyChange = function(){
                            $scope.amountModel = prices.quote_price * eval($scope.qtyModel);
                        }
                    });                    

                }

            });
        });                                     
    }          

/*    $scope.currentPage = 1;
    $scope.itemsPerPage = 10; */ 

        //delete deals
        $scope.confirmDelete = function(id){
            console.log(id);
            var isConfirmDelete = confirm('Are you sure you want to this?');
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/deal/data/' + id ,
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
/*
function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
} */   

app.controller('transactionController', transactionController);
// app.controller('repeatController', repeatController);
