var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);


    var $person = $('.person');
    var $item = $('.item');
    var $amount = $('#amount');
    var $trans_id = $('#transaction_id');
    var $person_select = $('.person_select');

    function transactionController($scope, $http){
        $scope.selection = {};
        $scope.Math = window.Math;

            angular.element(document).ready(function () {
                $('.date').datetimepicker({
                    format: 'YYYY-MM-DD'
                });

                $person.select2();
                $item.select2({
                    placeholder: "Select Item...",
                });

               $(".qtyClass").keyup(multInputs);
               $(".quoteClass").keyup(multInputs);
               function multInputs() {
                "use strict";
                   var mult = 0;
                   // for each row:
                   $("tr.txtMult").each(function () {
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

            $http.get('/api/transaction/edit/' + $trans_id.val()).success(function(data) {
                $scope.delivery = data.delivery_fee;
                $scope.deals = data.deals;
                $scope.totalModel = data.total;
                $scope.subtotalModel = data.subtotal;
                $scope.taxModel = data.tax;
                $scope.totalqtyModel = data.transaction.total_qty;
                console.log(data);

                $scope.form = {
                    person: data.transaction.person.id,
                    name: data.transaction.person.name,
                    payterm: data.transaction.person.payterm,
                    cust_id: data.transaction.person.cust_id,
                    transremark: data.transaction.transremark ? data.transaction.transremark : data.transaction.person.remark,
                    del_address: data.transaction.del_address ? data.transaction.del_address : data.transaction.person.del_address,
                    bill_address: data.transaction.bill_address ? data.transaction.bill_address : data.transaction.person.bill_address,
                    del_postcode: data.transaction.del_postcode ? data.transaction.del_postcode : data.transaction.person.del_postcode,
                    attn_name: data.transaction.name ? data.transaction.name : data.transaction.person.name,
                    contact: data.transaction.contact ? data.transaction.contact : data.transaction.person.contact,
                    order_date: data.transaction.order_date ? data.transaction.order_date : moment().format("YYYY-MM-DD"),
                    delivery_date: data.transaction.delivery_date ? data.transaction.delivery_date : moment().format("YYYY-MM-DD"),
                }

                $scope.onPrevSingleClicked = function(modelName, date) {
                    $scope.form[modelName] = moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD');
                }

                $scope.onNextSingleClicked = function(modelName, date) {
                    $scope.form[modelName] = moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD');
                }

                $scope.dateChanged = function(modelName, date) {
                    $scope.form[modelName] = moment(new Date(date)).format('YYYY-MM-DD');
                }

            });

/*            $http({
                url: '/transaction/' + $trans_id.val(),
                method: "GET",
            }).success(function(transaction){

                $scope.delivery = transaction.delivery_fee
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
                        });

                $http({
                    url: '/transaction/person/'+ transaction.person_id,
                    method: "GET",
                }).success(function(person){

                    $scope.form = {
                        person: person.id,
                        name: person.name,
                        payterm: person.payterm,
                        cust_id: person.cust_id,
                        transremark: transaction.transremark ? transaction.transremark : person.remark,
                        del_address: transaction.del_address ? transaction.del_address : person.del_address,
                        bill_address: transaction.bill_address ? transaction.bill_address : person.bill_address,
                        del_postcode: transaction.del_postcode ? transaction.del_postcode : person.del_postcode,
                        attn_name: transaction.name ? transaction.name : person.name,
                        contact: transaction.contact ? transaction.contact : person.contact,
                        order_date: transaction.order_date ? transaction.order_date : moment().format("YYYY-MM-DD"),
                        delivery_date: transaction.delivery_date ? transaction.delivery_date : moment().format("YYYY-MM-DD"),
                    }



                    $http({
                        url: '/transaction/item/'+ person.id,
                        method: "GET",
                    }).success(function(items){
                        $scope.items = items;
                    });
                });
            });*/
        // });

        //delete deals
        $scope.confirmDelete = function($event, deal_id){
            $event.preventDefault();
            var isConfirmDelete = confirm('Are you sure you want to this?');
            if(isConfirmDelete){
                $http.delete('/api/deal/delete/' + deal_id).success(function(data) {
                    location.reload();
                });
            }else{
                return false;
            }
        }
    }



app.filter('removeZero', ['$filter', function($filter) {
    return function(input) {
        input = parseFloat(input);
        input = input.toFixed(input % 1 === 0 ? 0 : 2);
        return input.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };
}]);

app.controller('transactionController', transactionController);

