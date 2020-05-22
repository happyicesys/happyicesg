var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

    function transController($scope, $http, $window){
        // init the variables
        $scope.alldata = [];
        $scope.users = [];
        $scope.coordsArr = [];
        $scope.checkbox = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
        $scope.driverOptionShowing = true;
        $scope.showBatchFunctionPanel = false;
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.requestfrom = moment().subtract(7, 'd').format("YYYY-MM-DD");
        $scope.requestto = moment().add(30, 'd').format("YYYY-MM-DD");
        $scope.search = {
            transaction_id: '',
            cust_id: '',
            company: '',
            status: '',
            statuses: '',
            pay_status: '',
            updated_by: '',
            updated_at: '',
            delivery_from: $scope.today,
            delivery_to: $scope.today ,
            requested_from: $scope.requestfrom,
            requested_to: $scope.requestto,
            driver: '',
            custcategory: [],
            exclude_custcategory: '',
            p_category: '',
            person_active: [],
            do_po: '',
            requester_name: '',
            pickup_location_name: '',
            delivery_location_name: '',
            area_groups: '',
            is_gst_inclusive: '',
            gst_rate: '',
            tags: [],
            transactions_row: '',
            itemsPerPage: 200,
            sortName: '',
            sortBy: true
        }
        $scope.updated_at = '';
        $scope.show_acc_consolidate_div = false;
        $scope.form = {
            person_account: '',
            driver: '-1',
            delivery_date: $scope.today
        };
        // init page load
        getUsersData();
        getPage(1);
        setShowRowFalseInit();

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });
            $('.selectmultiplecustcat').select2({
                placeholder: 'Choose one or many..'
            });
            $('#checkAll').change(function(){
                var all = this;
                $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
            });
        });

        $scope.exportData = function (event) {
            event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "JobAssign"+ now + ".xls");
        };

        $scope.dateChange = function(scope_from, date){
            if(date){
                $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
                $scope.compareDateChange(scope_from);
            }
            $scope.searchDB();
        }
        $scope.formDeliveryDateChange = function(scope, date){
            if(date){
                $scope.form[scope] = moment(new Date(date)).format('YYYY-MM-DD');
            }
        }
/*
        $scope.delToChange = function(scope_todate){
            if(date){
                $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        } */

        $scope.dateChange2 = function(date){
            if(date){
                $scope.search.updated_at = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }

        $scope.onPrevDateClicked = function(scope_from, scope_to) {
            $scope.search[scope_from] = moment(new Date($scope.search[scope_from])).subtract(1, 'days').format('YYYY-MM-DD');
            $scope.search[scope_to] = moment(new Date($scope.search[scope_to])).subtract(1, 'days').format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onTodayDateClicked = function(scope_from, scope_to) {
            $scope.search[scope_from] = moment().format('YYYY-MM-DD');
            $scope.search[scope_to] = moment().format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onNextDateClicked = function(scope_from, scope_to) {
            $scope.search[scope_from] = moment(new Date($scope.search[scope_from])).add(1, 'days').format('YYYY-MM-DD');
            $scope.search[scope_to] = moment(new Date($scope.search[scope_to])).add(1, 'days').format('YYYY-MM-DD');
            $scope.searchDB();
        }

        $scope.onPrevSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.compareDateChange(scope_name);
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.compareDateChange(scope_name);
            $scope.searchDB();
        }

        $scope.compareDateChange = function(scope_name) {
            if(scope_name === 'delivery_from') {
                $scope.search.delivery_to = $scope.search[scope_name];
            }

            if(scope_name === 'delivery_to') {
                if(moment($scope.search[scope_name]) < moment($scope.search.delivery_from)) {
                    $scope.search.delivery_from = $scope.search[scope_name];
                }
            }
        }

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1)
        };

        $scope.sortTable = function(sortName, driverkey) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            sortDriverTable(driverkey);
        }

          // when hitting search button
        $scope.searchDB = function(){
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1);
        }

        // enable acc consolidate div
        $scope.enableAccConsolidate = function(event) {
            event.preventDefault();
            $scope.show_acc_consolidate_div = !$scope.show_acc_consolidate_div;
        }

        // show hide transaction row assign driver dropdown and individual map button
        $scope.onDriverAssignToggleClicked = function(event) {
            event.preventDefault();
            $scope.driverOptionShowing = !$scope.driverOptionShowing;
        }

        // driver batch button dropdown
        $scope.onBatchFunctionClicked = function(event) {
            event.preventDefault();
            $scope.showBatchFunctionPanel = ! $scope.showBatchFunctionPanel;
        }

        // batch assign driver
        $scope.onBatchAssignDriverClicked = function(event) {
            event.preventDefault();
            $http.post('/api/transaction/batch/jobdriver',
            {
                delivery_date: $scope.search.delivery_from,
                drivers : $scope.drivers,
                driver: $scope.form.driver
            }).success(function(data) {
                getPage();
                $scope.form.checkall = false;
            });
        }

        // batch delivery date change
        $scope.onBatchChangeDeliveryDateClicked = function(event) {
            event.preventDefault();
            $http.post('/api/transaction/batch/deliverydate/jobassign',
            {
                drivers : $scope.drivers,
                delivery_date: $scope.form.delivery_date
            }).success(function(data) {
                getPage(1);
                $scope.form.checkall = false;
            });
        }

        // change transaction sequence
        $scope.onSequenceChanged = function(transaction, driverkey, transactionkey) {
            $http.post('/api/transaction/sequence/' + transaction.id, transaction).success(function(data) {
                // getPage();
            });
        }

        $scope.onInitTransactionsSequence = function(event) {
            event.preventDefault();
            let isConfirm = confirm('Are you sure to generate sequence based on this arrangement?')
            if(isConfirm) {
                $http.post('/api/transaction/initsequence', {drivers: $scope.drivers}).success(function(data) {
                    getPage();
                    $scope.form.checkall = false;
                });
            }else {
                return false;
            }
        }

        $scope.onDriverRowToggleClicked = function(event, driverkey) {
            event.preventDefault();
            $scope.drivers[driverkey].showrow = !$scope.drivers[driverkey].showrow;
        }

        $scope.onDriverRefreshClicked = function(event, driverkey) {
            event.preventDefault();
            $http.post('/api/transaction/jobassign/refreshdriver', {driverkey: driverkey, drivers: $scope.drivers}).success(function(data) {
                $scope.drivers = data;
            });
        }

        // driver whatsapp button clicked
        $scope.onWhatsappClicked = function(transaction) {
            let contactnumber = ''
            let text = ''
            if(transaction.contact) {
                contactnumber = transaction.contact.split(" ").join("")
                po_no = transaction.po_no
                $window.open(encodeURI('https://wa.me/65' + contactnumber + '?text=This is Happy Ice, we are delivering your ice cream order PO "' + po_no + '" in 90 mins time.'), '_blank');
            }
        }

        $scope.onCustCategoryChanged = function() {
            if($scope.search.custcategory.includes("55")) {
                $scope.search.p_category = true;
            }else {
                $scope.search.p_category = false;
            }
            $scope.searchDB();
        }

        $scope.onPCategoryChanged = function() {
            if($scope.search.p_category) {
                $scope.search.custcategory.push("55");
            }else {
                $scope.search.custcategory.splice($scope.search.custcategory.indexOf("55"), 1 );
            }
            $scope.searchDB();
        }

        // retrieve page w/wo search
        function getPage(pageNumber = null){
            $scope.spinner = true;
            $http.post('/api/transaction/jobassign', $scope.search).success(function(data){
                $scope.drivers = data.drivers;
                $scope.grand_total = data.grand_total ? data.grand_total : 0.00;
                $scope.grand_qty = data.grand_qty ? data.grand_qty : 0.00;
                $scope.grand_count = data.grand_count ? data.grand_count : 0;
                $scope.grand_delivered_total = data.grand_delivered_total ? data.grand_delivered_total : 0.00;
                $scope.grand_delivered_qty = data.grand_delivered_qty ? data.grand_delivered_qty : 0.00;
                $scope.grand_delivered_count = data.grand_delivered_count ? data.grand_delivered_count : 0;
                setShowRowDriverTrue();
                $('.selectmultiplecustcat').select2({
                    placeholder: 'Choose one or many..'
                });
                $scope.spinner = false;
            }).error(function(data){

            });
        }

        function sortDriverTable(driverkey) {
            $scope.spinner = true;
            console.log($scope.search.sortBy)
            $http.post('/api/transaction/jobassign/sortdrivertable', {
                driver: $scope.drivers[driverkey],
                sortName: $scope.search.sortName,
                sortBy: $scope.search.sortBy,
                driverKey: driverkey
            }).success(function(data) {
                console.log(data);
                $scope.drivers[driverkey] = data;
            });
            $scope.spinner = false;
        }

        function setShowRowFalseInit() {
            angular.forEach($scope.drivers, function(value, key) {
                $scope.drivers[key].showrow = false;
                if(value['name'] == $scope.search.driver) {
                    $scope.drivers[key].showrow = true;
                }
            })
        }

        function setShowRowDriverTrue() {
            angular.forEach($scope.drivers, function(value, key) {
                if(value['name'] == $scope.search.driver) {
                    $scope.drivers[key].showrow = true;
                }
            })
        }

        function getUsersData() {
            $http.get('/user/driver/active').success(function(data) {
                $scope.users = data;
            });
        }

        //delete record
        $scope.confirmDelete = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/transaction/data/' + id
                }).success(function(data){
                    location.reload();
                }).error(function(data){
                    alert('Unable to delete');
                })
            }else{
                return false;
            }
        }

        $scope.onFormDriverChanged = function(transaction, driverkey, transactionkey) {
            $http.post('/api/transaction/driver/quickupdate/jobassign', {transaction: transaction, delivery_date: $scope.delivery_from}).success(function(data) {
                $scope.drivers[driverkey].transactions[transactionkey].driver = data.driver;
                $scope.drivers[driverkey].transactions[transactionkey].updated_by = data.updated_by;
                $scope.drivers[driverkey].transactions[transactionkey].updated_at = data.updated_at;

                getPage();
            });
        }

        $scope.onIsImportantClicked = function(transaction_id, driverkey, transactionkey) {
            $http.post('/api/transaction/is_important/' + transaction_id).success(function(data) {
                $scope.drivers[driverkey].transactions[transactionkey].is_important = data.is_important;
            });
        }

        // checkbox all
        $scope.onCheckAllChecked = function(driverkey) {
            var checked = $scope.form.checkall[driverkey];

            angular.forEach($scope.drivers[driverkey].transactions, function(value, key) {
                $scope.drivers[driverkey].transactions[key].check = checked;
            });
        }

        // on driver init
        $scope.onDriverInit = function(driver_name) {
            $scope.search.driver = driver_name;
            // console.log($scope.drivers);
        }

        // on transaction remarks change
        $scope.onTransRemarkChanged = function(transaction_id, driverkey, transactionkey) {
            $http.post('/api/transaction/transremark/' + transaction_id, {transremark: $scope.drivers[driverkey].transactions[transactionkey].transremark}).success(function(data) {
            });
        }

        $scope.onMapClicked = function(singleperson = null, driverkey = null, transactionkey = null) {
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

            if(url.includes("my")) {
                location = 'Malaysia';
                locationLatLng = {lat: 1.4927, lng: 103.7414};
            }else if(url.includes("sg")) {
                location = 'Singapore';
                locationLatLng = {lat: 1.3521, lng: 103.8198};
            }

            var map = new google.maps.Map(document.getElementById('map'), {
                center: locationLatLng,
                zoom: 12
            });

            var geocoder = new google.maps.Geocoder();

            var markers = [];

            if(singleperson) {
                var contentString = '<span style=font-size:10px;>' +
                    '<span style="font-size:13px">' + '<b>' + singleperson.del_postcode + '; ' + singleperson.custcategory + '</b>' + '</span>' +
                    '<br>' +
                    '<b>' + singleperson.id + '</b>' + ', ' + singleperson.cust_id + ', ' + singleperson.company +
                    '<br>' +
                    singleperson.del_address;

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });
                // console.log(singleperson)
                $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + singleperson.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(data) {
                    let coord = {
                        transaction_id: singleperson.id,
                        lat: data.results[0].LATITUDE,
                        lng: data.results[0].LONGITUDE,
                    }
                    $http.post('/api/transaction/storelatlng/' + singleperson.id, coord).success(function (data) {
                        $scope.drivers[driverkey].transactions[transactionkey].del_lat = data.del_lat;
                        $scope.drivers[driverkey].transactions[transactionkey].del_lng = data.del_lng;

                        let url = map_icon_base + MAP_ICON_FILE[singleperson.map_icon_file]
                        var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company,
                            label: {fontSize: '15px', text: '' + singleperson.sequence * 1, fontWeight: 'bold'},
                            icon: {
                                labelOrigin: new google.maps.Point(15,10),
                                url: url
                            }
                        });
                        markers.push(marker);
                        marker.addListener('click', function () {
                            infowindow.open(map, marker);
                        });
                    });
                });
            }else {
                if(driverkey !== null) {
                    $scope.coordsArr = [];
                    angular.forEach($scope.drivers[driverkey].transactions, function(person, pkey) {
                        var contentString = '<span style=font-size:10px;>' +
                        '<span style="font-size:13px">' + '<b>' + person.del_postcode + '; ' + person.custcategory + '</b>' + '</span>' +
                        '<br>' +
                        '<b>' + person.id + '</b>' + ', ' + person.cust_id + ', ' + person.company +
                        '<br>' +
                        person.del_address;

                        var infowindow = new google.maps.InfoWindow({
                            content: contentString
                        });

                        if(!person.del_lat && !person.del_lng) {
                            $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + person.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(data) {
                                let coord = {
                                    transaction_id: person.id,
                                    lat: data.results[0].LATITUDE,
                                    lng: data.results[0].LONGITUDE,
                                }
                                $scope.coordsArr.push(coord)
                                $http.post('/api/transaction/storelatlng/' + person.id, coord).success(function (data) {
                                    $scope.drivers[driverkey].transactions[pkey].del_lat = data.del_lat;
                                    $scope.drivers[driverkey].transactions[pkey].del_lng = data.del_lng;
                                });
                            });
                        }

                        let url = map_icon_base + MAP_ICON_FILE[person.map_icon_file]
                        var pos = new google.maps.LatLng(person.del_lat, person.del_lng);
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: '(' + person.id + ') ' + person.cust_id + ' - ' + person.company,
                            label: {fontSize: '15px', text: '' + person.sequence * 1, fontWeight: 'bold'},
                            icon: {
                                labelOrigin: new google.maps.Point(15,10),
                                url: url
                            }
                        });
                        markers.push(marker);
                        marker.addListener('click', function () {
                            infowindow.open(map, marker);
                        });
                    });

                }else {
                    $scope.coordsArr = [];
                    angular.forEach($scope.drivers, function(driver, dkey) {
                        angular.forEach($scope.drivers[dkey].transactions, function(person, pkey) {
                            var contentString = '<span style=font-size:10px;>' +
                                '<span style="font-size:13px">' + '<b>#' + person.sequence + ', ' + person.del_postcode + '</span>' + '</b>' +
                                '<br>' +
                                '<b>' + person.id + '</b>' + ' ' + person.cust_id + ', ' + person.company +
                                '<br>' +
                                person.del_address;

                            var infowindow = new google.maps.InfoWindow({
                                content: contentString
                            });

                            if(!person.del_lat && !person.del_lng) {
                                $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + person.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(data) {
                                    let coord = {
                                        transaction_id: person.id,
                                        lat: data.results[0].LATITUDE,
                                        lng: data.results[0].LONGITUDE,
                                    }
                                    $scope.coordsArr.push(coord)
                                    $http.post('/api/transaction/storelatlng/' + person.id, coord).success(function (data) {
                                        $scope.drivers[dkey].transactions[pkey].del_lat = data.del_lat;
                                        $scope.drivers[dkey].transactions[pkey].del_lng = data.del_lng;
                                    });
                                });
                            }

                            let url = map_icon_base + MAP_ICON_FILE[person.map_icon_file]
                            var pos = new google.maps.LatLng(person.del_lat, person.del_lng);
                            var marker = new google.maps.Marker({
                                position: pos,
                                map: map,
                                title: '(' + person.id + ') ' + person.cust_id + ' - ' + person.company,
                                label: {fontSize: '15px', text: person.custcategory, fontWeight: 'bold'},
                                icon: {
                                    labelOrigin: new google.maps.Point(15,10),
                                    url: url
                                }
                            });
                            markers.push(marker);
                            marker.addListener('click', function () {
                                infowindow.open(map, marker);
                            });
                        });
                    });
                }
            }


            $("#mapModal").on("shown.bs.modal", function () {
                google.maps.event.trigger(map, "resize");
                map.setCenter(locationLatLng);
            });
        }
    }

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.filter('cut', function () {
    return function (value, wordwise, max, tail) {
        if (!value) return '';

        max = parseInt(max, 10);
        if (!max) return value;
        if (value.length <= max) return value;

        value = value.substr(0, max);
        if (wordwise) {
            var lastspace = value.lastIndexOf(' ');
            if (lastspace !== -1) {
              //Also remove . and , so its gives a cleaner result.
              if (value.charAt(lastspace-1) === '.' || value.charAt(lastspace-1) === ',') {
                lastspace = lastspace - 1;
              }
              value = value.substr(0, lastspace);
            }
        }

        return value + (tail || ' …');
    };
});

app.filter('myTrimFraction', ['$filter', function($filter) {
    return function(input) {
        input = parseFloat(input);
        input = input.toFixed(input % 1 === 0 ? 0 : 2);
        return input.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };
}]);

app.controller('transController', transController);