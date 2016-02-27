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
                       var $qty = eval($('.qtyClass', this).val());

                       var $quote = (+$('.quoteClass', this).val());

                       var $retail = (+$('.retailClass', this).val());

                       var $price = 0;

                       if($quote == null || $quote == '' || $quote == 0){

                            $price = 0;

                       }else{

                            $price = $quote;

                       }

                       var $total = (+$qty * +$price);
                       // set total for the row
                       // $('.amountClass', this).text($total);
                        if(isNaN($total)) {
                            var $total = 0;
                        }                   
                       $('.amountClass', this).val($total.toFixed(2));
                       mult += (+$total);
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
                    var totalqty = 0;
                    for(var i = 0; i < $scope.deals.length; i++){
                        var deal = $scope.deals[i];
                        total += (deal.amount/100*100);
                        totalqty += (deal.qty/100*100);
                    }

                        $http({
                            url: '/person/profile/' + transaction.person_id,
                            method: "GET",
                        }).success(function(profile){ 
    
                                $scope.totalModel = total;
                                $scope.totalqtyModel = totalqty;

                            if(profile.gst){

                                $scope.totalModelStore = (total * 7/100) + total;

                            }else{

                                $scope.totalModelStore = total;
                                    
                            }
/*                            
                            if(! $scope.totalModelStore == transaction.total){

                                $http.put('total', $scope.totalModelStore)
                                    .success(function(){
                                });                                 

                            }
 

                            $http.put('totalqty', $scope.totalqtyModel)
                                .success(function(){
                            });                                                                            
*/
                        });

                $http({
                    url: '/transaction/person/'+ transaction.person_id,
                    method: "GET",
                }).success(function(person){
                    $scope.personModel = person.id;
                    $scope.nameModel = person.name;
                    $scope.billModel = person.bill_address;
                    $scope.paytermModel = person.payterm;
                    $scope.personcodeModel = person.cust_id;
                    $scope.contactModel = person.contact;
                    $scope.attNameModel = person.name;

                    // choose which to display
                    // transremark
                    if(transaction.transremark){
                        
                        $scope.transremarkModel = transaction.transremark;

                    }else{

                        $scope.transremarkModel = person.remark;    
                    }

                    // delivery address
                    if(transaction.del_address){

                        $scope.delModel = transaction.del_address;

                    }else{

                        $scope.delModel = person.del_address + ' ' + person.del_postcode;
                    }
                    
                    $('.date').datetimepicker({
                        format: 'YYYY-MM-DD'
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

        // previous on select real time select cust function
/*
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
        }  */        

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

app.controller('transactionController', transactionController);

