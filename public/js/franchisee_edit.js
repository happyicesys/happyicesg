var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);


    var $person = $('.person');
    var $item = $('.item');
    var $amount = $('#amount');
    var $trans_id = $('#ftransaction_id');
    var $person_select = $('.person_select');

    function ftransactionController($scope, $http){
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

            function loadDealTable() {
                $http.get('/api/franchisee/edit/' + $trans_id.val()).success(function(data) {
                    $scope.delivery = data.delivery_fee;
                    $scope.fdeals = data.fdeals;
                    $scope.totalModel = data.total;
                    $scope.subtotalModel = data.subtotal;
                    $scope.taxModel = data.tax;
                    $scope.totalqtyModel = data.ftransaction.total_qty;

                    $scope.form = {
                        person: data.ftransaction.person.id,
                        name: data.ftransaction.person.name,
                        payterm: data.ftransaction.person.payterm,
                        cust_id: data.ftransaction.person.cust_id,
                        transremark: data.ftransaction.transremark ? data.ftransaction.transremark : data.ftransaction.person.remark,
                        del_address: data.ftransaction.del_address ? data.ftransaction.del_address : data.ftransaction.person.del_address,
                        bill_address: data.ftransaction.bill_address ? data.ftransaction.bill_address : data.ftransaction.person.bill_address,
                        del_postcode: data.ftransaction.del_postcode ? data.ftransaction.del_postcode : data.ftransaction.person.del_postcode,
                        attn_name: data.ftransaction.name ? data.ftransaction.name : data.ftransaction.person.name,
                        contact: data.ftransaction.contact ? data.ftransaction.contact : data.ftransaction.person.contact,
                        order_date: data.ftransaction.order_date ? data.ftransaction.order_date : moment().format("YYYY-MM-DD"),
                        delivery_date: data.ftransaction.delivery_date ? data.ftransaction.delivery_date : moment().format("YYYY-MM-DD"),
                    }
                });
            }

            loadDealTable();

            $scope.onPrevSingleClicked = function(modelName, date) {
                $scope.form[modelName] = moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD');
            }

            $scope.onNextSingleClicked = function(modelName, date) {
                $scope.form[modelName] = moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD');
            }

            $scope.dateChanged = function(modelName, date) {
                $scope.form[modelName] = moment(new Date(date)).format('YYYY-MM-DD');
            }

/*            $http({
                url: '/ftransaction/' + $trans_id.val(),
                method: "GET",
            }).success(function(ftransaction){

                $scope.delivery = ftransaction.delivery_fee
                $http({
                    url: '/deal/data/' + ftransaction.id,
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
                            url: '/person/profile/' + ftransaction.person_id,
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
                    url: '/ftransaction/person/'+ ftransaction.person_id,
                    method: "GET",
                }).success(function(person){

                    $scope.form = {
                        person: person.id,
                        name: person.name,
                        payterm: person.payterm,
                        cust_id: person.cust_id,
                        transremark: ftransaction.transremark ? ftransaction.transremark : person.remark,
                        del_address: ftransaction.del_address ? ftransaction.del_address : person.del_address,
                        bill_address: ftransaction.bill_address ? ftransaction.bill_address : person.bill_address,
                        del_postcode: ftransaction.del_postcode ? ftransaction.del_postcode : person.del_postcode,
                        attn_name: ftransaction.name ? ftransaction.name : person.name,
                        contact: ftransaction.contact ? ftransaction.contact : person.contact,
                        order_date: ftransaction.order_date ? ftransaction.order_date : moment().format("YYYY-MM-DD"),
                        delivery_date: ftransaction.delivery_date ? ftransaction.delivery_date : moment().format("YYYY-MM-DD"),
                    }



                    $http({
                        url: '/ftransaction/item/'+ person.id,
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
                $http.delete('/api/franchise/fdeal/delete/' + deal_id).success(function(data) {
                    loadDealTable();
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

app.controller('ftransactionController', ftransactionController);

