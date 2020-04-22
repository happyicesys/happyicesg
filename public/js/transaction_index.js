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
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.headerTemp = '';
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
            delivery_to: $scope.today,
            requested_from: $scope.requestfrom,
            requested_to: $scope.requestto,
            driver: '',
            custcategory: '',
            exclude_custcategory: '',
            person_active: [],
            do_po: '',
            requester_name: '',
            pickup_location_name: '',
            delivery_location_name: '',
            area_groups: '',
            is_gst_inclusive: '',
            gst_rate: '',
            tags: [],
            itemsPerPage: 200,
            sortName: '',
            sortBy: true
        }
        $scope.updated_at = '';
        $scope.show_acc_consolidate_div = false;
        $scope.form = {
            person_account: ''
        };
        // init page load
        getUsersData();
        getPage(1);

        angular.element(document).ready(function () {
            $('.select').select2();
            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
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
            }
            $scope.searchDB();
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
            $scope.searchDB();
        }

        $scope.onNextSingleClicked = function(scope_name, date) {
            $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $scope.searchDB();
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

                // return total amount
                $scope.total_amount = data.total_amount;
                $scope.spinner = false;
            }).error(function(data){

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

        $scope.onMapClicked = function(singleperson = null) {
            var url = window.location.href;
            var location = '';
            var locationLatLng = {};

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
                geocoder.geocode(
                    {
                        componentRestrictions: { country: location, postalCode: singleperson.del_postcode }
                    }, function (results, status) {
                        if (results[0]) {
                            if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                                setTimeout(3000);
                            }
                            var contentString = '<span style=font-size:10px;>' +
                                '<b>' +
                                '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company +
                                '</b>' +
                                '<br>' +
                                singleperson.del_address +
                                '</span>';

                            var infowindow = new google.maps.InfoWindow({
                                content: contentString
                            });
                            var marker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: map,
                                title: singleperson.cust_id + ' - ' + singleperson.company
                            });
                            markers.push(marker);
                            marker.addListener('click', function () {
                                infowindow.open(map, marker);
                            });
                            var jsondata = JSON.parse(JSON.stringify(results[0].geometry.location));
                            var coord = {
                                lat: jsondata.lat,
                                lng: jsondata.lng
                            };
                            $http.post('/api/transaction/storelatlng/' + singleperson.id, coord).success(function (data) {});
                        }
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
                        person.del_address +
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

                    var pos = new google.maps.LatLng(person.del_lat, person.del_lng);
                    var marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: '(' + person.id + ') ' + person.cust_id + ' - ' + person.company
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

app.controller('transController', transController);