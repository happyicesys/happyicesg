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

function transactionController($scope, $http) {
    $scope.selection = {};
    $scope.Math = window.Math;
    $today = moment().format('YYYY-MM-DD');
    $scope.assetform = {
        personasset_id: '',
        personasset_qty: '',
        transactionpersonasset_id: ''
    }
    $scope.assetformitems = [];
    $scope.transactionpersonassetform = {
        id: '',
        code: '',
        name: '',
        brand: '',
        serial_no: '',
        sticker: '',
        remarks: ''
    }
    $scope.doform = {
        job_type: '',
        po_no: '',
        submission_datetime: '',
        pickup_date: '',
        pickup_timerange: '',
        pickup_attn: '',
        pickup_contact: '',
        pickup_location_name: '',
        pickup_address: '',
        pickup_postcode: '',
        pickup_comment: '',
        delivery_date1: '',
        delivery_timerange: '',
        delivery_attn: '',
        delivery_contact: '',
        delivery_location_name: '',
        delivery_address: '',
        delivery_postcode: '',
        delivery_comment: '',
        transaction_id: '',
        requester: '',
        requester_name: '',
        requester_contact: '',
        requester_notification_emails: ''
    }
    $scope.formService = {
        desc: '',
        before: '',
        after: '',
    }
    $scope.jobtypeSelection = [
        {
            id: 'Delivery_Job',
            name: 'Delivery Job'
        },
        {
            id: 'OnSite_Troubleshooting',
            name: 'OnSite Troubleshooting'
        },
    ]

    $scope.requesterSelections = [
        {
            id: 'Clement Chon',
            name: 'Clement Chon',
            contact: '97891437',
            email: 'Clement.Chon@genmills.com'
        },
        {
            id: 'Corrine Chong',
            name: 'Corrine Chong',
            contact: '90611680',
            email: 'Corrine.Chong@genmills.com'
        },
        {
            id: 'Eric Tay',
            name: 'Eric Tay',
            contact: '63056792',
            email: 'Eric.Tay@genmills.com'
        },
        {
            id: 'Jenny',
            name: 'Jenny',
            contact: '93886631',
            email: 'Jenny.Sim@genmills.com'
        },
        {
            id: 'Kian Poh',
            name: 'Kian Poh',
            contact: '91700759',
            email: 'kianpoh.ng@genmills.com'
        },
        {
            id: 'Sook Hui',
            name: 'Sook Hui',
            contact: '98008063',
            email: 'sookhui.choo@genmills.com'
        },
        {
            id: 'Xin Yi Seng',
            name: 'Xin Yi Seng',
            contact: '97750109',
            email: 'Seng.xinyi@genmills.com'
        }
    ];

    $scope.showpersonassetSelection = true;
    $scope.hideSignature = true;
    $scope.errors = [];
    $scope.files = [];
    $scope.service = {};
    $scope.currentAttachmentId = '';
    $scope.attachmentType = '';
    $scope.showServiceCompletionError = false;
    $scope.priceTemplateItems = [];
    $scope.priceItems = [];
    $scope.transaction;
    $scope.uoms;
    $scope.totalAmount = 0.00;
    var formData = new FormData();

    loadDealTable();
    transactionpersonasset();

    angular.element(document).ready(function () {
        loadServiceTable($trans_id.val());
        $('.date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $person.select2();
        $item.select2({
            placeholder: "Select Item...",
        });
        $('.selectassetform').select2({
            placeholder: 'Please Select'
        });
        $('.selectNormal').select2();

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
                if ($quote == null || $quote == '' || $quote == 0) {
                    $price = 0;
                } else {
                    $price = $quote;
                }
                var $total = (+$qty * +$price);
                if (isNaN($total)) {
                    var $total = 0;
                }
                $('.amountClass', this).val($total.toFixed(2));
                mult += (+$total);
            });
            $('.grandTotal').val(mult.toFixed(2));
        }

        $('.btn-number').click(function (e) {
            e.preventDefault();

            fieldName = $(this).attr('data-field');
            type = $(this).attr('data-type');
            var input = $("input[name='" + fieldName + "']");
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                if (type == 'minus') {

                    if (currentVal > input.attr('min')) {
                        input.val(currentVal - 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('min')) {
                        $(this).attr('disabled', true);
                    }

                } else if (type == 'plus') {

                    if (currentVal < input.attr('max')) {
                        input.val(currentVal + 1).change();
                    }
                    if (parseInt(input.val()) == input.attr('max')) {
                        $(this).attr('disabled', true);
                    }

                }
            } else {
                input.val(0);
            }
        });
        $('.input-number').focusin(function () {
            $(this).data('oldValue', $(this).val());
        });
        $('.input-number').change(function () {

            minValue = parseInt($(this).attr('min'));
            maxValue = parseInt($(this).attr('max'));
            valueCurrent = parseInt($(this).val());

            name = $(this).attr('name');
            if (valueCurrent >= minValue) {
                $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
            } else {
                alert('Sorry, the minimum value was reached');
                $(this).val($(this).data('oldValue'));
            }
            if (valueCurrent <= maxValue) {
                $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
            } else {
                alert('Sorry, the maximum value was reached');
                $(this).val($(this).data('oldValue'));
            }


        });
        $(".input-number").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        var canvas = document.querySelector("canvas");

        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 3
        });


        $scope.submitSignature = function () {
            var signdata = signaturePad.toDataURL();
            $http.post('/transaction/signature/submit/' + $trans_id.val(), { 'data': signdata }).success(function (data) {
                loadDealTable();
            });
            location.reload();
        }

        $scope.clearSignature = function () {
            // Clears the canvas
            signaturePad.clear();
        }
    });

    $http.get('/person/data').success(function (people) {
        $scope.people = people;
    });

    $scope.onIsImportantClicked = function (transaction_id, index, driver_role = false) {
        // console.log(driver_role)
        if (!driver_role) {
            $http.post('/api/transaction/is_important/' + transaction_id).success(function (data) {
                location.reload();
            });
        }
    }

    function getServiceDefaultForm() {
        return {
            id: '',
            status: '',
            desc: '',
            attachment1: '',
            attachment2: '',
        }
    }

    function loadServiceTable(transactionId) {
        $http.get('/api/transaction/' + transactionId + '/services').success(function (data) {
            $scope.services = data.services;
            // console.log($scope.services)
            if ($scope.services.length < 5) {
                var total = 5 - $scope.services.length;
                for (var i = 0; i < total; i++) {
                    $scope.services.push(getServiceDefaultForm());
                }
            }
        });
    }


    function loadDealTable() {
        $http.get('/api/transaction/edit/' + $trans_id.val()).success(function (data) {
            $scope.priceTemplateItems = data.priceTemplateItems;
            $scope.delivery = data.delivery_fee;
            $scope.deals = data.deals;
            $scope.totalModel = data.total;
            $scope.subtotalModel = data.subtotal;
            $scope.taxModel = data.tax;
            $scope.totalqtyModel = data.total_qty.toFixed(4);
            $scope.isStockAction = data.isStockAction;
            $scope.priceItems = data.priceItems;
            $scope.transaction = data.transaction;
            $scope.uoms = data.uoms;


            // console.log($scope.totalModel);
            // console.log($scope.subtotalModel);
            // console.log($scope.taxModel);

            $scope.getTotalPieces = function () {
                var total = 0;
                for (var i = 0; i < data.deals.length; i++) {
                    var deal = data.deals[i];
                    total += (+deal.pieces);
                }
                return total;
            }

            $scope.form = {
                person: data.transaction.person.id,
                name: data.transaction.person.name,
                payterm: data.transaction.person.payterm,
                cust_id: data.transaction.person.cust_id,
                transremark: data.transaction.transremark ? data.transaction.transremark : '',
                person_remark: data.transaction.person.remark ? data.transaction.person.remark : '',
                del_address: data.transaction.del_address ? data.transaction.del_address : data.transaction.person.del_address,
                bill_address: data.transaction.bill_address ? data.transaction.bill_address : data.transaction.person.bill_address,
                del_postcode: data.transaction.del_postcode ? data.transaction.del_postcode : data.transaction.person.del_postcode,
                bill_postcode: data.transaction.bill_postcode ? data.transaction.bill_postcode : data.transaction.person.bill_postcode,
                billing_country_id: data.transaction.billing_country_id ? data.transaction.billing_country_id : data.transaction.person.billing_country_id,
                delivery_country_id: data.transaction.delivery_country_id ? data.transaction.delivery_country_id : data.transaction.person.delivery_country_id,
                attn_name: data.transaction.name ? data.transaction.name : data.transaction.person.name,
                contact: data.transaction.contact ? data.transaction.contact : data.transaction.person.contact,
                order_date: data.transaction.order_date ? data.transaction.order_date : moment().format("YYYY-MM-DD"),
                delivery_date: data.transaction.delivery_date ? data.transaction.delivery_date : moment().format("YYYY-MM-DD"),
                sign_url: data.transaction.sign_url,
                is_deliveryorder: data.transaction.is_deliveryorder
            }

            if (data.transaction.deliveryorder) {
                var dodata = data.transaction.deliveryorder;
                $scope.doform = {
                    job_type: dodata.job_type,
                    po_no: dodata.po_no,
                    requester_name: dodata.requester_name,
                    requester_contact: dodata.requester_contact,
                    submission_datetime: dodata.submission_datetime ? moment(dodata.submission_datetime).format('YYYY-MM-DD   hh:mm A') : '',
                    pickup_date: dodata.pickup_date ? moment(dodata.pickup_date).format('YYYY-MM-DD') : $today,
                    pickup_timerange: dodata.pickup_timerange,
                    pickup_attn: dodata.pickup_attn,
                    pickup_contact: dodata.pickup_contact,
                    pickup_location_name: dodata.pickup_location_name,
                    pickup_address: dodata.pickup_address,
                    pickup_postcode: dodata.pickup_postcode,
                    pickup_comment: dodata.pickup_comment,
                    delivery_date1: dodata.delivery_date1 ? moment(dodata.delivery_date1).format('YYYY-MM-DD') : $today,
                    delivery_timerange: dodata.delivery_timerange,
                    delivery_attn: dodata.delivery_attn,
                    delivery_contact: dodata.delivery_contact,
                    delivery_location_name: dodata.delivery_location_name,
                    delivery_address: dodata.delivery_address,
                    delivery_postcode: dodata.delivery_postcode,
                    delivery_comment: dodata.delivery_comment,
                    transaction_id: dodata.transaction_id,
                    requester: dodata.requester,
                    from_happyice: dodata.from_happyice == 1 ? true : false,
                    to_happyice: dodata.to_happyice == 1 ? true : false,
                    requester_notification_emails: dodata.requester_notification_emails
                }

                if ($scope.doform.from_happyice) {
                    $scope.showpersonassetSelection = false;
                }

                if ($scope.doform.to_happyice) {
                    $scope.showpersonassetSelection = true;
                }
            }
        });
    }


    function transactionpersonasset() {
        $http.get('/api/transactionpersonasset/index/' + $trans_id.val()).success(function (data) {
            $scope.alldata = data.data;
        });
    }

    $scope.onPrevSingleClicked = function (modelName, date) {
        $scope.form[modelName] = moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD');
    }

    $scope.onNextSingleClicked = function (modelName, date) {
        $scope.form[modelName] = moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD');
    }

    $scope.dateChanged = function (modelName, date) {
        $scope.form[modelName] = moment(new Date(date)).format('YYYY-MM-DD');
    }

    //delete deals
    $scope.confirmDelete = function ($event, deal_id) {
        $event.preventDefault();
        var isConfirmDelete = confirm('Are you sure you want to this?');
        if (isConfirmDelete) {
            $http.delete('/api/deal/delete/' + deal_id).success(function (data) {
                loadDealTable();
            });
        } else {
            return false;
        }
    }

    $scope.onAssetqtyChanged = function () {
        $scope.assetformitems = [];
        var i;

        for (i = 0; i < $scope.assetform.personasset_qty; i++) {
            $scope.assetformitems.push({
                serial_no: '',
                sticker: '',
                remarks: ''
            });
        }
    }

    $scope.submitTransactionpersonasset = function () {
        $http.post('/api/transactionpersonasset/create', {
            items: $scope.assetformitems,
            personasset_id: $scope.assetform.personasset_id,
            transactionpersonasset_id: $scope.assetform.transactionpersonasset_id,
            transaction_id: $('#transaction_id').val(),
            qty: $scope.assetform.personasset_qty
        }).success(function (data) {
            $scope.assetformitems = [];
            $scope.assetform = {
                personasset_id: '',
                personasset_qty: '',
                transactionpersonasset_id: ''
            }
            $('.selectassetform').val(null).trigger('change.select2');
            transactionpersonasset();
            return data;
        })
    }

    function clearTransactionpersonassetform() {
        $scope.transactionpersonassetform = {
            id: '',
            code: '',
            name: '',
            brand: '',
            serial_no: '',
            sticker: '',
            remarks: '',
        }
    }


    $scope.removeTransactionpersonassetEntry = function ($event, id, showpersonassetSelection) {
        $event.stopPropagation();
        $event.preventDefault();
        var isConfirmDelete = confirm('Are you sure to DELETE this item?');
        if (isConfirmDelete) {
            $http.delete('/api/transactionpersonasset/' + id + '/delete').success(function (data) {
                transactionpersonasset();
            });
        } else {
            return false;
        }
    }

    $scope.editTransactionpersonassetModal = function ($event, transactionpersonasset) {
        // $event.stopPropagation();
        $event.preventDefault();
        fetchSingleTransactionpersonasset(transactionpersonasset);
    }

    function fetchSingleTransactionpersonasset(data) {
        $scope.transactionpersonassetform = {
            id: data.id,
            code: data.code,
            name: data.name,
            brand: data.brand,
            serial_no: data.serial_no,
            sticker: data.sticker,
            remarks: data.remarks
        }
    }

    $scope.updateTransactionpersonasset = function ($event) {
        $event.preventDefault();
        $http.post('/api/transactionpersonasset/update', $scope.transactionpersonassetform).success(function (data) {
            transactionpersonasset();
        });
    }

    $scope.onFromHappyiceChanged = function () {

        if ($scope.doform.from_happyice) {
            $scope.doform.pickup_attn = 'Kent';
            $scope.doform.pickup_contact = '96977973';
            $scope.doform.pickup_location_name = 'Happy Ice';
            $scope.doform.pickup_address = 'Blk 2021 #01-198 Bukit Batok St 23';
            $scope.doform.pickup_postcode = '659526';
            $scope.doform.to_happyice = false;
            $scope.showpersonassetSelection = false;
        } else {
            $scope.doform.pickup_attn = '';
            $scope.doform.pickup_contact = '';
            $scope.doform.pickup_location_name = '';
            $scope.doform.pickup_address = '';
            $scope.doform.pickup_postcode = '';
            $scope.showpersonassetSelection = true;
        }
        $scope.assetformitems = [];
        $scope.assetform = {
            personasset_id: '',
            personasset_qty: '',
            transactionpersonasset_id: ''
        }
        $('.selectassetform').val(null).trigger('change.select2');
    }

    $scope.onToHappyiceChanged = function () {
        if ($scope.doform.to_happyice) {
            $scope.doform.delivery_attn = 'Kent';
            $scope.doform.delivery_contact = '96977973';
            $scope.doform.delivery_location_name = 'Happy Ice';
            $scope.doform.delivery_address = 'Blk 2021 #01-198 Bukit Batok St 23';
            $scope.doform.delivery_postcode = '659526';
            $scope.doform.from_happyice = false;
        } else {
            $scope.doform.delivery_attn = '';
            $scope.doform.delivery_contact = '';
            $scope.doform.delivery_location_name = '';
            $scope.doform.delivery_address = '';
            $scope.doform.delivery_postcode = '';
        }
        $scope.showpersonassetSelection = true;
        $scope.assetformitems = [];
        $scope.assetform = {
            personasset_id: '',
            personasset_qty: '',
            transactionpersonasset_id: ''
        }
        $('.selectassetform').val(null).trigger('change.select2');
    }

    $scope.onPickupDate = function (date) {
        // console.log('here');
        if (date) {
            $scope.doform.pickup_date = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

    $scope.onSignatureCaretClicked = function () {
        $scope.hideSignature = !$scope.hideSignature;
    }

    $scope.deleteSignature = function () {
        $http.get('/transaction/signature/delete/' + $trans_id.val()).success(function (data) {
            loadDealTable();
        });
    }

    $scope.requesterNameChanged = function () {
        var requester_name = $scope.doform.requester_name;
        for (var i = 0; i < $scope.requesterSelections.length; i++) {
            var looprequester = $scope.requesterSelections[i];
            if (looprequester.name == requester_name) {
                $scope.doform.requester_contact = looprequester.contact;
                $scope.doform.requester_notification_emails = looprequester.email;
            }
        }
    }

    $scope.onNewServiceClicked = function ($event) {
        $event.preventDefault();
        $scope.services.push(getServiceDefaultForm());
    }

    $scope.editService = function ($event, serviceItem) {
        $event.preventDefault();
        $scope.formService = serviceItem;
    }

    $scope.uploadExcel = function (event) {
        event.preventDefault();
        var request = {
            method: 'POST',
            url: '/api/transaction/excel/import',
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement = angular.element('#attachment1');
                fileElement.value = '';
                if (e.data === 'true') {
                    alert("Excel file uploaded and transactions loaded");
                } else {
                    alert("Invoice or Item creation failure, please refer to the Result file");
                }
                $scope.searchDB();
            }, function error(e) {
                $scope.errors = e.data.errors;
                alert('Upload unsuccessful, please make sure only have one excel sheet, check the customer id, and try again')
            });
    };

    $scope.setAttachment1 = function ($files, serviceId, reload = false) {
        angular.forEach($files, function (value, key) {
            formData.append('attachment1', value);
        });
        formData.append('desc', $scope.formService.desc);
        uploadFile('/api/transaction/service/' + serviceId + '/update');
        if (reload) {
            loadServiceTable($trans_id.val());
            // location.reload();
        }
    };

    $scope.setAttachment2 = function ($files, serviceId, reload = false) {
        angular.forEach($files, function (value, key) {
            formData.append('attachment2', value);
        });
        formData.append('desc', $scope.formService.desc);
        uploadFile('/api/transaction/service/' + serviceId + '/update');
        if (reload) {
            loadServiceTable($trans_id.val());
            // location.reload();
        }
    };

    function uploadFile(url) {
        var request = {
            method: 'POST',
            url: url,
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                location.reload();
            }, function error(e) {
                $scope.errors = e.data.errors;
                alert('Upload unsuccessful, please try again')
            });
    }

    $scope.onServiceSubmitClicked = function (event, transactionId) {
        formData.append('desc', $scope.formService.desc);
        event.preventDefault();
        var request = {
            method: 'POST',
            url: '/api/transaction/' + transactionId + '/service/store',
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement1 = angular.element('#attachment1');
                var fileElement2 = angular.element('#attachment2');
                fileElement1.value = '';
                fileElement2.value = '';
                if (e.data === 'true') {
                    alert("Entry created");
                }
                location.reload();
            }, function error(e) {
                $scope.errors = e.data.errors;
                alert('Upload unsuccessful, please try again')
            });
    }

    $scope.onServiceUpdated = function (event, serviceId) {
        formData.append('desc', $scope.formService.desc);
        event.preventDefault();
        var request = {
            method: 'POST',
            url: '/api/transaction/service/' + serviceId + '/update',
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        };
        $http(request)
            .then(function success(e) {
                $scope.files = e.data.files;
                $scope.errors = [];
                // clear uploaded file
                var fileElement1 = angular.element('#attachment1');
                var fileElement2 = angular.element('#attachment2');
                fileElement1.value = '';
                fileElement2.value = '';
                if (e.data === 'true') {
                    alert("Entry created");
                }
                location.reload();
            }, function error(e) {
                $scope.errors = e.data.errors;
                alert('Upload unsuccessful, please try again')
            });
    }

    $scope.removeAttachment = function (event, formServiceId, formServiceAttachmentId) {
        // event.preventDefault();
        $http.post('/api/transaction/service/' + formServiceId + '/attachment/' + formServiceAttachmentId + '/delete').success(function (data) {
            loadServiceTable($trans_id.val());
            location.reload();
        });
    }

    $scope.deleteService = function (event, formServiceId) {
        $http.post('/api/transaction/service/' + formServiceId + '/delete').success(function (data) {
            loadServiceTable($trans_id.val());
            location.reload();
        });
    }

    $scope.cancelService = function (event, formServiceId) {
        $http.post('/api/transaction/service/' + formServiceId + '/cancel').success(function (data) {
            loadServiceTable($trans_id.val());
            location.reload();
        });
    }

    $scope.completeService = function (event, formServiceId) {
        $http.post('/api/transaction/service/' + formServiceId + '/complete').success(function (data) {
            loadServiceTable($trans_id.val());
            location.reload();
        });
    }

    $scope.onServiceDescChanged = function (serviceIndex) {
        $http.post('/api/transaction/service/sync', { service: $scope.services[serviceIndex], transactionId: $trans_id.val() }).success(function (data) {
            if (!$scope.services[serviceIndex].id || !$scope.services[serviceIndex].desc) {
                loadServiceTable($trans_id.val());
            }
        });
    }

    $scope.onStatusClicked = function (event, serviceId, statusCode) {
        event.preventDefault();
        $http.post('/api/transaction/service/' + serviceId + '/status', { statusCode: statusCode }).success(function (data) {
            loadServiceTable($trans_id.val());
        });
    }

    $scope.onAttachmentModalClicked = function (service, isPrimary = false) {
        $scope.service = service;
        $scope.attachmentType = isPrimary;
    }

    $scope.downloadAttachment = function (event, attachmentId) {
        event.preventDefault();
        $http.get('/api/transaction/service/attachment/' + attachmentId).success(function (data) {
        });
    }

    $scope.onCancelConfirmationClicked = function (event) {
        event.preventDefault();
        $http.post('/api/transaction/' + $trans_id.val() + '/cancelConfirmation', { form_delete: 'form_delete', cancelForm: $scope.cancelForm }).success(function (data) {
            location.reload();
        });
    }

    $scope.onIsSameAddressChecked = function () {
        if ($scope.form.is_same_address) {
            $scope.form.del_postcode = $scope.form.bill_postcode;
            $scope.form.del_address = $scope.form.bill_address;
            $scope.form.delivery_country_id = $scope.form.billing_country_id;
        } else {
            $scope.form.del_postcode = '';
            $scope.form.del_address = '';
            $scope.form.delivery_country_id = 2;
        }
        $('.selectNormal').select2();
    }

    $scope.onStockButtonClicked = function (event, inventoryMovementType, extraDealProductId = []) {
        event.preventDefault();
        $http.post('/api/transaction/' + $trans_id.val() + '/sync-stock-action-deals', { inventoryMovementType: inventoryMovementType, extraDealProductId: extraDealProductId }).success(function (data) {
            loadDealTable();
        });
    }

    $scope.checkIsActiveUom = function (uomId, priceItem) {
        let isActive = false;
        if (priceItem.price_template_item_uoms.length) {
            angular.forEach(priceItem.price_template_item_uoms, function (value, key) {
                if (value.item_uom && value.item_uom.uom.id == uomId) {
                    isActive = true;
                }
            });
        }
        if (uomId == 3 && priceItem.item.is_inventory == 0) {
            isActive = true;
        }
        return isActive;
    }

    $scope.syncAmount = function (priceItem) {
        // console.log(priceItem);
        let subTotalAmount = 0;
        let grandTotalAmount = 0.00;
        if (priceItem.qty) {
            if (!priceItem.item.is_inventory || !$scope.transaction.person.price_template) {
                oldQtyValue = priceItem.qty['ctn'] ? eval(priceItem.qty['ctn']) : 0;
                subTotalAmount = oldQtyValue * priceItem.quote_price;
            } else {
                let baseUom = {}
                let transactedUom = {}
                angular.forEach(priceItem.item.item_uoms, function (itemUom, itemUomKey) {
                    if (itemUom && itemUom.is_base_unit) {
                        baseUom = itemUom;
                    }
                    if (itemUom && itemUom.is_transacted_unit) {
                        transactedUom = itemUom;
                    }
                });
                angular.forEach(priceItem.qty, function (qtyValue, qtyKey) {
                    qtyValue = qtyValue ? qtyValue : 0;
                    angular.forEach(priceItem.price_template_item_uoms, function (itemUom, itemUomKey) {
                        if (itemUom.item_uom && qtyKey == itemUom.item_uom.uom.name) {
                            subTotalAmount += (parseInt(qtyValue) * parseInt(itemUom.item_uom.value)) / parseInt(transactedUom.value) * parseFloat(priceItem.quote_price);
                        }
                    });
                });
            }

        }
        priceItem.amount = subTotalAmount.toFixed(2);

        if ($scope.priceItems) {
            angular.forEach($scope.priceItems, function (priceItem, priceItemIndex) {
                grandTotalAmount += priceItem.amount ? parseFloat(priceItem.amount) : 0;
            });
            $scope.totalAmount = grandTotalAmount.toFixed(2);
        }
    }
}


app.filter('removeZero', ['$filter', function ($filter) {
    return function (input) {
        input = parseFloat(input);
        input = input.toFixed(input % 1 === 0 ? 0 : 2);
        return input.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };
}]);

app.directive('ngFiles', ['$parse', function ($parse) {

    function file_links(scope, element, attrs) {
        var onChange = $parse(attrs.ngFiles);
        element.on('change', function (event) {
            onChange(scope, { $files: event.target.files });
        });
    }
    return {
        link: file_links
    }
}]);

app.directive('ngConfirmClick', [
    function () {
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmClick || "Are you sure?";
                var clickAction = attr.confirmedClick;
                element.bind('click', function (event) {
                    if (window.confirm(msg)) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
    }]);

app.filter('trusted', ['$sce', function ($sce) {
    return function (url) {
        return $sce.trustAsResourceUrl(url);
    };
}]);

app.controller('transactionController', transactionController);
