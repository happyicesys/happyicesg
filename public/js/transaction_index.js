var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

    function transController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.users = [];
        $scope.coordsArr = [];
        $scope.checkbox = [];
        $scope.excelHistories = [];
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
            creator_id: '',
            itemsPerPage: 200,
            sortName: '',
            sortBy: true
        }
        $scope.updated_at = '';
        $scope.show_acc_consolidate_div = false;
        $scope.form = {
            person_account: '',
            driver: '-1',
            delivery_date: $scope.today,
            excel_file: [],
            pay_status: '',
            paid_at: $scope.today,
            pay_method: '',
            note: ''
        };
        // init page load
        getUsersData();
        getPage(1);

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectform').select2({
                placeholder: 'Select...'
            });
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
            saveAs(blob, "TransactionRpt"+ now + ".xls");
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
                if(moment($scope.search[scope_name]) > moment($scope.search.delivery_to)) {
                    $scope.search.delivery_to = $scope.search[scope_name];
                }
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

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1);
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

        $scope.onBatchAssignDriverClicked = function(event) {
            event.preventDefault();
            $http.post('/api/transaction/batchdriver',
            {
                transactions : $scope.alldata,
                driver: $scope.form.driver
            }).success(function(data) {
                getPage(1);
                $scope.form.checkall = false;
            });
        }

        $scope.onBatchChangeDeliveryDateClicked = function(event) {
            event.preventDefault();
            $http.post('/api/transaction/batch/deliverydate',
            {
                transactions : $scope.alldata,
                delivery_date: $scope.form.delivery_date
            }).success(function(data) {
                getPage(1);
                $scope.form.checkall = false;
            });
        }

        // batch payment status and paid at
        $scope.onBatchSetPaymentClicked = function(event) {
            event.preventDefault();
            $http.post('/api/transaction/batch/paymentstatus', {
                transactions: $scope.alldata,
                chosen: $scope.form
            }).success(function(data) {
                $scope.searchDB();
                $scope.form.checkall = false;
            })
        }

        // retrieve page w/wo search
        function getPage(pageNumber){
            $scope.spinner = true;
            $http.post('/transaction/data?page=' + pageNumber, $scope.search).success(function(data){
                if(data.transactions.data){
                    $scope.alldata = data.transactions.data;
                    $scope.totalCount = data.transactions.total;
                    $scope.currentPage = data.transactions.current_page;
                    $scope.indexFrom = data.transactions.from;
                    $scope.indexTo = data.transactions.to;
                }else{
                    $scope.alldata = data.transactions;
                    $scope.totalCount = data.transactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.transactions.length;
                }
                // get total count
                $scope.All = data.transactions.length;
                $('.selectmultiplecustcat').select2({
                    placeholder: 'Choose one or many..'
                });
                // return total amount
                $scope.total_amount = data.total_amount;
                $scope.spinner = false;
            }).error(function(data){
            });

        $http.get('/api/transaction/excel/histories').success(function(data) {
            $scope.excelHistories = data;
        });
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

        $scope.onFormDriverChanged = function(transaction, index) {
            $http.post('/api/transaction/driver/quickupdate', transaction).success(function(data) {
                $scope.alldata[index].driver = data.driver;
                $scope.alldata[index].updated_by = data.updated_by;
                $scope.alldata[index].updated_at = data.updated_at;
            });
        }

        $scope.onIsImportantClicked = function(transaction_id, index) {
            $http.post('/api/transaction/is_important/' + transaction_id).success(function(data) {
                $scope.alldata[index].is_important = data.is_important;
            });
        }

        // checkbox all
        $scope.onCheckAllChecked = function() {
            var checked = $scope.form.checkall;

            $scope.alldata.forEach(function (transaction, key) {
                $scope.alldata[key].check = checked;
            });
        }

        $scope.errors = [];
        $scope.files = [];
        var formData = new FormData();

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
                    var fileElement = angular.element('#excel_file');
                    fileElement.value = '';
                    if(e.data === 'true') {
                        alert("Excel file uploaded and transactions loaded");
                    }else {
                        alert("Invoice or Item creation failure, please refer to the Result file");
                    }
                    $scope.searchDB();
                }, function error(e) {
                    $scope.errors = e.data.errors;
                    alert('Upload unsuccessful, please make sure only have one excel sheet, check the customer id, and try again')
                });
        };

        $scope.setTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formData.append('excel_file', value);
            });
        };

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


        $scope.onMapClicked = function(singleperson = null, index = null) {
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
                    '<b>' +
                    '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company +
                    '</b>' +
                    '<br>' +
                    '<span style="font-size:13px">' + '<b>' + singleperson.del_postcode + '</b>' + '</span>' + ' ' + singleperson.del_address +
                    '</span>';

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
                        $scope.alldata[index].del_lat = data.del_lat;
                        $scope.alldata[index].del_lng = data.del_lng;

                        let url = map_icon_base + MAP_ICON_FILE[singleperson.map_icon_file]
                        var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company,
                            label: {fontSize: '15px', text: singleperson.custcategory, fontWeight: 'bold'},
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
                $scope.coordsArr = [];
                $scope.alldata.forEach(function (person, key) {
                    // var address = person.del_address.replace(/ /g, '+');
                    var contentString = '<span style=font-size:10px;>' +
                        '<b>' +
                        '(' + person.id + ') ' + person.cust_id + ' - ' + person.company +
                        '</b>' +
                        '<br>' +
                        '<span style="font-size:13px">' + '<b>' + person.del_postcode + '</b>' + '</span>' + ' ' + person.del_address +
                        '</span>';

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
                                $scope.alldata[key].del_lat = data.del_lat;
                                $scope.alldata[key].del_lng = data.del_lng;
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

app.directive('ngFiles', ['$parse', function ($parse) {

    function file_links(scope, element, attrs) {
        var onChange = $parse(attrs.ngFiles);
        element.on('change', function (event) {
            onChange(scope, {$files: event.target.files});
        });
    }
    return {
        link: file_links
    }
}]);

app.controller('transController', transController);