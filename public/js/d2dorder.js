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
            // for each row:
            $("tr.txtMult").each(function () {
                // get the values from this row:
                var $item = $('.itemClass option:selected', this).val();

                var $qty = $('.qtyClass option:selected', this).val();

                var $price = 0;

                switch($item){

                    case '1':
                    case '2':
                    case '3':
                    case '4':
                        $price = 7.90;
                        break;

                    case '5':
                    case '6':
                    case '7':
                        $price = 8.50;
                        break;

                    case '8':
                        $price = 7.90;
                        break;

                    case '9':
                        $price = 9.50;
                        break;
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
    }

app.controller('d2dorderController', d2dorderController);
