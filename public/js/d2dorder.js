var app = angular.module('app', []);

    $('.select').select2();

    function d2dorderController($scope, $http){
        $(document).on('change', '.itemClass' ,function() {
            multInputs();
        });

        $(document).on('change', '.qtyClass' ,function() {
            multInputs();
        });

        $(document).on('click', '.removeClass' ,function() {
            var countTotal = 0;
            $(this).parent().parent().remove();
            $(".rowCount").each(function (i){
                $(this).text(i+1);
            });
            multInputs();
        });

        function multInputs() {
        "use strict";
            var mult = 0;
            var totalqty = 0;
            // for each row:
            $("tr.txtMult").each(function () {
                // get the values from this row
                var $qty = $('.qtyClass option:selected', this).val();
                var $price = (+$('.priceClass', this).val());
                var $total = (+$qty * +$price);

                // set total for the row
                // $('.amountClass', this).text($total);
                if(isNaN($total)) {
                    var $total = 0;
                }

                $('.amountClass', this).val($total.toFixed(2));
                mult += (+$total);
                totalqty += (+$qty);
            });
            $('.delfeeTotal').val(totalqty.toFixed(2))
            $('.grandTotal').val(mult.toFixed(2));
       }
    }

app.controller('d2dorderController', d2dorderController);
