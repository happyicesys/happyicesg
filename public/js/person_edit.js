var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    // 'ui.select2',
    'ngSanitize',
    '720kb.datepicker',
    'datePicker',
]);

function personEditController($scope, $http) {

    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 20;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        id: '',
        statuses: ['Pending', 'Confirmed', 'Delivered'],
        pay_status: '',
        delivery_from: '',
        delivery_to: '',
        driver: '',
        is_service: '',
        pageNum: 20,
    }

    $scope.allVendData = [];
    $scope.totalVendCount = 0;
    $scope.totalVendPages = 0;
    $scope.currentVendPage = 1;
    $scope.indexVendFrom = 0;
    $scope.indexVendTo = 0;
    $scope.vendItemsPerPage = 20;
    $scope.searchvend = {
        id: '',
        collection_from: '',
        collection_to: '',
        itemsPerPage: 20,
        sortName: '',
        person_id: $('#person_id').val(),
        sortBy: true
    }

    $scope.total_amount = 0.00;
    $scope.total_paid = 0.00;
    $scope.total_owe = 0.00;
    $scope.outcomes = [];
    $scope.outletvisitForm = {}
    $scope.todayDate = moment().format('YYYY-MM-DD');
    $scope.todayDay = moment().format('ddd');
    $scope.form = {
        cooperate_method: '',
        commission_type: '',
        commission_package: '',
        vending_piece_price: '',
        vending_monthly_rental: '',
        vending_profit_sharing: '',
        vending_monthly_utilities: '',
        vending_clocker_adjustment: '',
        serial_number: '',
        terminal_id: '',
        terminal_provider: '',
        cms_serial_number: '',
        key_lock_number: '',
        serial_no: '',
        is_parent: '',
        is_vend: '',
    }
    $scope.commissionOptions = [
        {
            id: 1,
            name: 'Absolute Amount'
        },
        {
            id: 2,
            name: 'Percentage'
        },
    ]
    $scope.commissionPackages = [
        {
            id: 1,
            name: 'Both Utility & Comm'
        },
        {
            id: 2,
            name: 'Whichever One is Higher'
        },
    ]

    // init page load
    getOutletVisitsPerson($('#person_id').val())
    getOutletVisitOutcomes();
    getPage(1, true);
    getPersonApi();
    loadFiles();

    angular.element(document).ready(function () {
        $('.select').select2({
            placeholder: 'Select..',
            allowClear: true
        });
        $('.selectNormal').select2();
        $('.selectnotclear').select2({
            placeholder: 'Select..'
        });
        // $('.selectmultiple').select2({});
        $('.selectmultiple').select2({
            tags: true,
            createTag: function (params) {
                return {
                    id: "New:" + params.term,
                    text: params.term,
                    newOption: true
                }
            }
        });
        toggleVendingFields();
        $('#cooperate_method').on('select2:select', function (e) {
            var data = e.params.data['id'];
            toggleVendingFields();
        });

        togglePwpRateField();
        $('#is_pwp').on('select2:select', function (e) {
            var data = e.params.data['id'];
            togglePwpRateField();
        });

        toggleIsVendField();
        $('#is_vend').on('select2:select', function (e) {
            var data = e.params.data['id'];
            toggleIsVendField();
        });
    });
    $scope.onDeliveryFromChanged = function (date) {
        if (date) {
            $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }
    $scope.onDeliveryToChanged = function (date) {
        if (date) {
            $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }
    $scope.exportDataTransRpt = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_trans').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "TransactionRpt" + now + ".xls");
    };

    // switching page
    $scope.pageChanged = function (newPage) {
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function () {
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1, false)
    };

    // when hitting search button
    $scope.searchDB = function () {
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    $scope.onOutletVisitClicked = function (event) {
        event.preventDefault();
        $scope.getOutletVisitPerson($('#person_id').val())
    }

    $scope.onOutletVisitDateChanged = function (date) {
        if (date) {
            let momentDate = moment(new Date(date)).format('YYYY-MM-DD');
            let momentDay = moment(new Date(date)).format('ddd');
            $scope.outletvisitForm.date = momentDate;
            $scope.outletvisitForm.day = momentDay;
        }
    }

    $scope.saveOutletVisitForm = function (event) {
        event.preventDefault();
        $http.post('/api/person/outletvisit/' + $('#person_id').val(), $scope.outletvisitForm).success(function (data) {
            $scope.getOutletVisitPerson($('#person_id').val())
            $scope.outletvisitForm.remarks = ''
            getPage(1, false);
        });
    }

    $scope.deleteOutletVisitEntry = function (id) {
        $http.delete('/api/person/outletvisit/' + id).success(function (data) {
            $scope.getOutletVisitPerson($('#person_id').val())
            getPage(1, false);
        });
    }

    $scope.getOutletVisitPerson = function (person_id) {
        getOutletVisitsPerson(person_id);
    }

    function getOutletVisitsPerson(person_id) {
        $http.post('/api/outletvisits/person/' + person_id).success(function (data) {
            $scope.outletvisitForm = {
                person: data,
                date: $scope.todayDate,
                day: $scope.todayDay,
                outcome: 1
            }
        });
    }

    function toggleVendingFields() {
        let cooperateMethod = $('#cooperate_method').val();
        if (cooperateMethod == 1) {
            $('.commissionDiv').show();
            $('.rentalDiv').hide();
        } else {
            $('.commissionDiv').hide();
            $('.rentalDiv').show();
        }
    }

    function togglePwpRateField() {
        let isPwp = $('#is_pwp').val();
        if (isPwp == 1) {
            $('.isPwpDiv').show();
        } else {
            $('.isPwpDiv').hide();
        }
    }

    function toggleIsVendField() {
        let isPwp = $('#is_vend').val();
        if (isPwp == 1) {
            $('.isVendDiv').show();
        } else {
            $('.isVendDiv').hide();
        }
    }

    // retrieve single person data
    function getPersonApi() {
        $http.post('/api/person/edit/' + $('#person_id').val()).success(function (data) {
            $scope.form = {
                ...data.people,
                commission_type: $scope.commissionOptions.find(x => x.id === data.people.commission_type),
                commission_package: $scope.commissionPackages.find(x => x.id === data.people.commission_package),
            };
            toggleVendingFields();
            togglePwpRateField();
            toggleIsVendField();

            // $('.select').select2({
            //     placeholder: 'Select..',
            //     allowClear: true
            // });
            // $('.selectnotclear').select2({
            //     placeholder: 'Select..'
            // });
            // console.log(JSON.parse(JSON.stringify(data)))
        });
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.transactionSpinner = true;
        $http.post('/person/transac/' + $('#person_id').val() + '?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.transactions.data) {
                $scope.alldata = data.transactions.data;
                $scope.totalCount = data.transactions.total;
                $scope.currentPage = data.transactions.current_page;
                $scope.indexFrom = data.transactions.from;
                $scope.indexTo = data.transactions.to;
            } else {
                $scope.alldata = data.transactions;
                $scope.totalCount = data.transactions.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.transactions.length;
            }
            // get total count
            $scope.All = data.transactions.length;

            // return total amount
            $scope.total_amount = data.total_amount;
            $scope.total_paid = data.total_paid;
            $scope.total_owe = data.total_owe;
            $scope.profileDealsGrossProfit = data.profileDealsGrossProfit;
            $scope.transactionSpinner = false;
        });
        $http.get('/api/person/persontags/' + $('#person_id').val()).success(function (data) {
            $scope.persontags_options = data;
        });
    }

    $scope.onIsServiceClicked = function (transaction_id, index) {
        $http.post('/api/transaction/is_service/' + transaction_id + '/2').success(function (data) {
            $scope.alldata[index].is_service = data.is_service;
            getPage(1);
        });
    }

    // price management
    initPrice();

    function initPrice() {
        $http.get('/person/price/' + $('#person_id').val()).success(function (items) {
            $scope.items = items;
        });
        $http.get('/person/costrate/' + $('#person_id').val()).success(function (data) {
            $scope.costrate = data;
        });
    }

    $scope.calQuotePrice = function (index, item) {
        if (!isNaN(item.retail_price)) {
            $scope.items[index]['quote_price'] = item.retail_price * $scope.costrate / 100;
        } else {
            initPrice();
        }
    }

    $http.get('/person/specific/data/' + $('#person_id').val()).success(function (person) {
        $scope.personData = person;
        $scope.noteModel = person.note;

        $scope.getRetailChange = function (retailModel) {
            $scope.afterChange = (retailModel * person.cost_rate / 100).toFixed(2);
        }

    });

    // loading files from person
    function loadFiles() {
        $http.get('/api/person/files/' + $('#person_id').val()).success(function (data) {
            $scope.files = data;
        });
    }

    // removing file
    $scope.removeFile = function (file_id) {
        $http.post('/api/person/file/remove', { 'file_id': file_id }).success(function (data) {
            loadFiles();
        });
    }

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportableVend').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "FVendCash" + now + ".xls");
    };

    $scope.collectionFromChanged = function (date) {
        if (date) {
            $scope.searchvend.collection_from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchVendDB();
    }

    $scope.collectionToChanged = function (date) {
        if (date) {
            $scope.searchvend.collection_to = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchVendDB();
    }

    $scope.onPrevDateClicked = function () {
        $scope.searchvend.collection_from = moment(new Date($scope.searchvend.collection_from)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.searchvend.collection_to = moment(new Date($scope.searchvend.collection_to)).subtract(1, 'days').format('YYYY-MM-DD');
        $scope.searchVendDB();
    }

    $scope.onTodayDateClicked = function () {
        $scope.searchvend.collection_from = moment().format('YYYY-MM-DD');
        $scope.searchvend.collection_to = moment().format('YYYY-MM-DD');
        $scope.searchVendDB();
    }

    $scope.onNextDateClicked = function () {
        $scope.searchvend.collection_from = moment(new Date($scope.searchvend.collection_from)).add(1, 'days').format('YYYY-MM-DD');
        $scope.searchvend.collection_to = moment(new Date($scope.searchvend.collection_to)).add(1, 'days').format('YYYY-MM-DD');
        $scope.searchVendDB();
    }

    $scope.onPrevSingleClicked = function (scope_name, date) {
        $scope.searchvend[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchVendDB();
    }

    $scope.onNextSingleClicked = function (scope_name, date) {
        $scope.searchvend[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchVendDB();
    }

    // switching page
    $scope.vendPageChanged = function (newPage) {
        getVendPage(newPage);
    };

    $scope.vendPageNumChanged = function () {
        $scope.searchvend['pageNum'] = $scope.vendItemsPerPage
        $scope.currentVendPage = 1
        getVendPage(1);
    };

    $scope.sortVendTable = function (sortName) {
        $scope.searchvend.sortName = sortName;
        $scope.searchvend.sortBy = !$scope.searchvend.sortBy;
        getVendPage(1);
    }

    // when hitting search button
    $scope.searchVendDB = function () {
        $scope.searchvend.sortName = '';
        $scope.searchvend.sortBy = true;
        getVendPage(1);
    }

    $scope.changeRemarks = function (id, remarks) {
        $http.post('/api/franchisee/remarks/' + id, { 'remarks': remarks }).success(function (data) {
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

    $scope.onMapClicked = function (singleperson = null, index = null, type = null) {
        // singleperson = $('#person_id').val();
        singleperson = $scope.personData;
        var url = window.location.href;
        var location = '';
        var locationLatLng = {};
        let map_icon_base = 'http://maps.google.com/mapfiles/ms/micons/';
        const MAP_ICON_FILE = {
            'red': 'red.png',
            'blue': 'blue.png',
            'green': 'green.png',
            'light-blue': 'lightblue.png',
            'pink': 'pink.png',
            'purple': 'purple.png',
            'yellow': 'yellow.png',
            'orange': 'orange.png'
        };

        if (url.includes("my")) {
            location = 'Malaysia';
            locationLatLng = { lat: 1.4927, lng: 103.7414 };
        } else if (url.includes("sg")) {
            location = 'Singapore';
            locationLatLng = { lat: 1.3521, lng: 103.8198 };
        }

        var map = new google.maps.Map(document.getElementById('map'), {
            center: locationLatLng,
            zoom: 12
        });

        var geocoder = new google.maps.Geocoder();

        var markers = [];

        if (singleperson) {
            // console.log('here1');
            var contentString = '<span style=font-size:10px;>' +
                '<b>' +
                '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company +
                '</b>' +
                // '<br>' +
                // '<span style="font-size:13px">' + '<b>' + singleperson.del_postcode + '</b>' + '</span>' + ' ' + singleperson.del_address +
                '</span>';

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + singleperson.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function (data) {

                // console.log(singleperson)
                let coord = {
                    transaction_id: singleperson.id,
                    lat: data.results[0].LATITUDE,
                    lng: data.results[0].LONGITUDE,
                }
                $http.post('/api/person/storelatlng/' + singleperson.id, coord).success(function (data) {
                    $scope.personData.del_lat = data.del_lat;
                    $scope.personData.del_lng = data.del_lng;

                    let url = map_icon_base + MAP_ICON_FILE[singleperson.custcategory.map_icon_file]
                    var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                    if (type === 2) {
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: singleperson.cust_id + ' - ' + singleperson.company + ' - ' + singleperson.custcategory,
                            label: { fontSize: '13px', text: '(' + singleperson.cust_id + ') ' + singleperson.company, fontWeight: 'bold' },
                            icon: {
                                labelOrigin: new google.maps.Point(15, 10),
                                url: url
                            }
                        });
                    } else {
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: singleperson.cust_id + ' - ' + singleperson.company + ' - ' + singleperson.custcategory,
                            label: { fontSize: '15px', text: '(' + singleperson.cust_id + ') ' + singleperson.company, fontWeight: 'bold' },
                            icon: {
                                labelOrigin: new google.maps.Point(15, 10),
                                url: url
                            }
                        });
                    }
                    markers.push(marker);

                    marker.addListener('click', function () {
                        infowindow.open(map, marker);
                    });
                });
            });

        } else {
            $scope.coordsArr = [];
            $scope.alldata.forEach(function (person, key) {
                let custString = person.cust_id + ' - ' + person.company + ' - ' + person.custcategory;
                var contentString = '<span style=font-size:10px;>' +
                    '<b>' +
                    custString +
                    '</b>' +
                    // '<br>' +
                    // '<span style="font-size:13px">' + '<b>' + person.del_postcode + '</b>' + '</span>' + ' ' + person.del_address +
                    '</span>';

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });
                // console.log(person)
                if (!person.del_lat && !person.del_lng) {
                    $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + person.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function (data) {
                        let coord = {
                            transaction_id: person.id,
                            lat: data.results[0].LATITUDE,
                            lng: data.results[0].LONGITUDE,
                        }
                        $scope.coordsArr.push(coord)
                        $http.post('/api/person/storelatlng/' + person.id, coord).success(function (data) {
                            $scope.alldata[key].del_lat = data.del_lat;
                            $scope.alldata[key].del_lng = data.del_lng;
                        });
                    });
                }

                let url = map_icon_base + MAP_ICON_FILE[person.map_icon_file]
                var pos = new google.maps.LatLng(person.del_lat, person.del_lng);
                if (type === 2) {
                    var marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: person.cust_id + ' - ' + person.company + ' - ' + person.custcategory,
                        label: { fontSize: '13px', text: '(' + (key + $scope.indexFrom).toString() + ')' + custString, fontWeight: 'bold' },
                        icon: {
                            labelOrigin: new google.maps.Point(15, 10),
                            url: url
                        }
                    });
                } else {
                    var marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: person.cust_id + ' - ' + person.company + ' - ' + person.custcategory,
                        label: { fontSize: '15px', text: (key + $scope.indexFrom).toString(), fontWeight: 'bold' },
                        icon: {
                            labelOrigin: new google.maps.Point(15, 10),
                            url: url
                        }
                    });
                }

                markers.push(marker);

                marker.addListener('click', function () {
                    infowindow.open(map, marker);
                });

            });
        }


        $("#mapModal").on("shown.bs.modal", function () {
            google.maps.event.trigger(map, "resize");
            map.setCenter(locationLatLng);
        });
    }

    getVendPage(1);

    function getOutletVisitOutcomes() {
        $http.get('/api/outletvisit/outcomes').success(function (data) {
            $scope.outcomes = data;
        });
    }

    // retrieve page w/wo search
    function getVendPage(pageNumber) {
        $scope.spinner = true;
        $http.post('/api/franchisee?page=' + pageNumber, $scope.searchvend).success(function (data) {
            if (data.ftransactions.data) {
                $scope.allVendData = data.ftransactions.data;
                $scope.totalVendCount = data.ftransactions.total;
                $scope.currentVendPage = data.ftransactions.current_page;
                $scope.indexVendFrom = data.ftransactions.from;
                $scope.indexVendTo = data.ftransactions.to;
            } else {
                $scope.allVendData = data.ftransactions;
                $scope.totalVendCount = data.ftransactions.length;
                $scope.currentVendPage = 1;
                $scope.indexVendFrom = 1;
                $scope.indexVendTo = data.ftransactions.length;
            }
            // get total count
            $scope.VendAll = data.ftransactions.length;

            // return total amount
            $scope.total_vend_amount = data.totals.total_vend_amount;
            $scope.total_sales_pieces = data.totals.total_sales_pieces;
            $scope.avg_pieces_day = data.totals.avg_pieces_day;
            $scope.total_stock_in = data.totals.total_stock_in;
            $scope.total_sold_qty = data.totals.total_sold_qty;
            $scope.difference_stock_sold = data.totals.difference_stock_sold;
            $scope.transactions_total = data.totals.transactions_total;
            $scope.transactions_owe = data.totals.transactions_owe;
            $scope.transactions_paid = data.totals.transactions_paid;
            $scope.spinner = false;
        }).error(function (data) {

        });
    }

}

app.filter('delDate', [
    '$filter', function ($filter) {
        return function (input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.filter('capitalize', function () {
    return function (input, scope) {
        if (input != null) {
            input = input.toLowerCase();
            return input.substring(0, 1).toUpperCase() + input.substring(1);
        } else {
            return null;
        }
    }
});

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
    }])

function repeatController($scope) {
    $scope.$watch('$index', function (index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('personEditController', personEditController);
app.controller('repeatController', repeatController);
