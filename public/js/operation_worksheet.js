var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

    function operationWorksheetController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 'All';
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            profile_id: '',
            id_prefix: '',
            custcategory: '',
            exclude_custcategory: 0,
            cust_id: '',
            company: '',
            chosen_date: moment().format('YYYY-MM-DD'),
            previous: 'Last 5 days',
            future: '2 days',
            color: '',
            del_postcode: '',
            preferred_days: '',
            zones: '',
            tags: [],
            account_manager: '',
            pageNum: 'All',
            sortBy: true,
            sortName: ''
        }
        $scope.zones = [];
        // init page load
        // getPage(1, true);
        getZoneOptions();

        angular.element(document).ready(function () {
            $('.select').select2();

            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });

            $('.date').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        });

        $scope.exportData = function ($event) {
            $event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Operation Worksheet"+ now + ".xls");
        };

        $scope.onChosenDateChanged = function(date){
            if(date){
                $scope.search.chosen_date = moment(new Date(date)).format('YYYY-MM-DD');
            }
            // $scope.searchDB();
        }

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

          // when hitting search button
        $scope.searchDB = function(event){
            event.preventDefault();
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
        }

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1, false);
        }

        $scope.changeColor = function(alldata, parent_index, index) {
            if(!alldata.qty) {
                $http.post('/api/detailrpt/operation/color', {'id': alldata.id}).success(function(data) {
                    $scope.alldata[parent_index][index]['color'] = data.color;
                });
            }
        }

        $scope.todayDateChecker = function(date) {
            if(date === $scope.search.chosen_date) {
                return 'Lightpurple';
            }
        }

        $scope.updateOpsNotes = function(person_id, operation_note) {
            $http.post('/api/detailrpt/operation/note/' + person_id, {'operation_note': operation_note}).success(function(date) {
            });
        }

        $scope.getBackgroundColor = function(alldata, parent_index, index) {
/*
            if(alldata.bool_transaction) {
                if(!alldata.qty) {
                    $scope.alldata[parent_index][index]['qty'] = 0;
                }
                return '#77d867';
            }else { */
                if(alldata.color) {
                    return alldata.color;
                }else {
                    return '';
                }
            // }
        }

        $scope.toggleCheckbox = function(value, person_id, day) {
            $http.post('/api/operation/day', {
                value: value,
                person_id: person_id,
                day: day
            }).success(function(data) {
            });
        }

        $scope.toggleZoneCheckbox = function(value, person_id, area) {

            $http.post('/api/operation/area', {
                value: value,
                person_id: person_id,
                area: area
            }).success(function(data) {
            });
        }

        $scope.exportTransactions = function() {
            $http.post('/api/detailrpt/operation/batchinvoices', $scope.search).success(function(data) {
                alert('Invoices Created');
                searchDB();
            });
        }

        $scope.onPersonZoneChanged = function(person, index) {
            $http.post('/api/detailrpt/operation/zone', person).success(function(data) {
                $scope.people[index]['zone_name'] = person.zone_id['name'];
                $scope.people[index]['zone_id'] = person.zone_id['id'];
            });
        }

        $scope.onMapClicked = function(singleperson = null, index = null) {
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

                $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + singleperson.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(data) {
                    let coord = {
                        transaction_id: singleperson.id,
                        lat: data.results[0].LATITUDE,
                        lng: data.results[0].LONGITUDE,
                    }
                    $http.post('/api/person/storelatlng/' + singleperson.person_id, coord).success(function (data) {
                        $scope.people[index].del_lat = data.del_lat;
                        $scope.people[index].del_lng = data.del_lng;

                        var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company
                        });
                        markers.push(marker);
                        marker.addListener('click', function () {
                            infowindow.open(map, marker);
                        });
                    });
                });

            }else {
                $scope.coordsArr = [];
                $scope.people.forEach(function (person, key) {
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
                            $http.post('/api/person/storelatlng/' + person.person_id, coord).success(function (data) {
                                $scope.people[key].del_lat = data.del_lat;
                                $scope.people[key].del_lng = data.del_lng;
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

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/operation?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.people.data){
                    $scope.dates = data.dates;
                    $scope.alldata = data.alldata;
                    $scope.people = data.people.data;
                    $scope.totalCount = data.people.total;
                    $scope.currentPage = data.people.current_page;
                    $scope.indexFrom = data.people.from;
                    $scope.indexTo = data.people.to;
                }else{
                    $scope.dates = data.dates;
                    $scope.people = data.people;
                    $scope.alldata = data.alldata;
                    $scope.totalCount = data.people.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.people.length;
                }
                // get total count
                $scope.All = data.people.length;
                // return fixed total amount
                $scope.spinner = false;
            });
        }

        function replaceAt(string, index, replace) {
            return string.substring(0, index) + replace + string.substring(index + 1);
        }

        function getZoneOptions() {
            $http.get('/api/zones').success(function(data) {
                $scope.zones = data;
            });
        }
    }

    function merchandiserController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 'All';
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.search = {
            profile_id: '',
            id_prefix: '',
            custcategory: '',
            exclude_custcategory: 0,
            cust_id: '',
            company: '',
            chosen_date: moment().format('YYYY-MM-DD'),
            previous: 'Last 14 days',
            future: '2 days',
            color: '',
            del_postcode: '',
            preferred_days: '',
            zones: '',
            tags: [],
            account_manager: '',
            pageNum: 'All',
            sortBy: true,
            sortName: ''
        }
        $scope.form = {
            date: '',
            day: ''
        };
        $scope.todayDate = moment().format('YYYY-MM-DD');
        $scope.todayDay = moment().format('ddd');
        $scope.zones = [];
        // init page load
        // getPage(1, true);
        getZoneOptions();

        angular.element(document).ready(function () {
            $('.select').select2();

            $('.selectmultiple').select2({
                placeholder: 'Choose one or many..'
            });

            $('.date').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        });

        $scope.exportData = function ($event) {
            $event.preventDefault();
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "Operation Worksheet"+ now + ".xls");
        };

        $scope.onChosenDateChanged = function(date){
            if(date){
                $scope.search.chosen_date = moment(new Date(date)).format('YYYY-MM-DD');
            }
            // $scope.searchDB();
        }

        // switching page
        $scope.pageChanged = function(newPage){
            getPage(newPage, false);
        };

        $scope.pageNumChanged = function(){
            $scope.search['pageNum'] = $scope.itemsPerPage
            $scope.currentPage = 1
            getPage(1, false)
        };

          // when hitting search button
        $scope.searchDB = function(event){
            event.preventDefault();
            $scope.search.sortName = '';
            $scope.search.sortBy = true;
            getPage(1, false);
        }

        $scope.sortTable = function(sortName) {
            $scope.search.sortName = sortName;
            $scope.search.sortBy = ! $scope.search.sortBy;
            getPage(1, false);
        }

        $scope.changeColor = function(alldata, parent_index, index) {
            if(!alldata.qty) {
                $http.post('/api/detailrpt/operation/color', {'id': alldata.id}).success(function(data) {
                    $scope.alldata[parent_index][index]['color'] = data.color;
                });
            }
        }

        $scope.todayDateChecker = function(date) {
            if(date === $scope.search.chosen_date) {
                return 'Lightpurple';
            }
        }

        $scope.updateOpsNotes = function(person_id, operation_note) {
            $http.post('/api/detailrpt/operation/note/' + person_id, {'operation_note': operation_note}).success(function(date) {
            });
        }

        $scope.getBackgroundColor = function(alldata, parent_index, index) {
/*
            if(alldata.bool_transaction) {
                if(!alldata.qty) {
                    $scope.alldata[parent_index][index]['qty'] = 0;
                }
                return '#77d867';
            }else { */
                if(alldata.color) {
                    return alldata.color;
                }else {
                    return '';
                }
            // }
        }

        $scope.toggleCheckbox = function(value, person_id, day) {
            $http.post('/api/operation/day', {
                value: value,
                person_id: person_id,
                day: day
            }).success(function(data) {
            });
        }

        $scope.toggleZoneCheckbox = function(value, person_id, area) {

            $http.post('/api/operation/area', {
                value: value,
                person_id: person_id,
                area: area
            }).success(function(data) {
            });
        }

        $scope.exportTransactions = function() {
            $http.post('/api/detailrpt/operation/batchinvoices', $scope.search).success(function(data) {
                alert('Invoices Created');
                searchDB();
            });
        }

        $scope.onPersonZoneChanged = function(person, index) {
            $http.post('/api/detailrpt/operation/zone', person).success(function(data) {
                $scope.people[index]['zone_name'] = person.zone_id['name'];
                $scope.people[index]['zone_id'] = person.zone_id['id'];
            });
        }

        $scope.onOutletVisitClicked = function(event, person) {
            event.preventDefault();
            $scope.form = {
                person: person,
                date: $scope.todayDate,
                day: $scope.todayDay,
                outcome: 1
            }
        }

        $scope.onOutletVisitDateChanged = function(date) {
            if(date){
                let momentDate = moment(new Date(date)).format('YYYY-MM-DD');
                let momentDay = moment(new Date(date)).format('ddd');
                $scope.form.date = momentDate;
                $scope.form.day = momentDay;
            }
        }

        $scope.saveOutletVisitForm = function(person) {
            $http.post('/api/person/outletvisit/' + person.id, $scope.form).success(function(data) {
                $scope.form.person.outletVisits = data
            });
        }

        $scope.deleteOutletVisitEntry = function(id) {
            $http.delete('/api/person/outletvisit/' + id).success(function(data) {
                $scope.form.person.outletVisits = data
            });
        }

        $scope.onMapClicked = function(singleperson = null, index = null) {
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

                $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + singleperson.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(data) {
                    let coord = {
                        transaction_id: singleperson.id,
                        lat: data.results[0].LATITUDE,
                        lng: data.results[0].LONGITUDE,
                    }
                    $http.post('/api/person/storelatlng/' + singleperson.person_id, coord).success(function (data) {
                        $scope.people[index].del_lat = data.del_lat;
                        $scope.people[index].del_lng = data.del_lng;

                        var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: '(' + singleperson.id + ') ' + singleperson.cust_id + ' - ' + singleperson.company
                        });
                        markers.push(marker);
                        marker.addListener('click', function () {
                            infowindow.open(map, marker);
                        });
                    });
                });

            }else {
                $scope.coordsArr = [];
                $scope.people.forEach(function (person, key) {
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
                            $http.post('/api/person/storelatlng/' + person.person_id, coord).success(function (data) {
                                $scope.people[key].del_lat = data.del_lat;
                                $scope.people[key].del_lng = data.del_lng;
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

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/api/detailrpt/operation?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.people.data){
                    $scope.dates = data.dates;
                    $scope.alldata = data.alldata;
                    $scope.people = data.people.data;
                    $scope.totalCount = data.people.total;
                    $scope.currentPage = data.people.current_page;
                    $scope.indexFrom = data.people.from;
                    $scope.indexTo = data.people.to;
                }else{
                    $scope.dates = data.dates;
                    $scope.people = data.people;
                    $scope.alldata = data.alldata;
                    $scope.totalCount = data.people.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.people.length;
                }
                // get total count
                $scope.All = data.people.length;
                // return fixed total amount
                $scope.spinner = false;
            });
        }

        function replaceAt(string, index, replace) {
            return string.substring(0, index) + replace + string.substring(index + 1);
        }

        function getZoneOptions() {
            $http.get('/api/zones').success(function(data) {
                $scope.zones = data;
            });
        }
    }

app.controller('operationWorksheetController', operationWorksheetController);
app.controller('merchandiserController', merchandiserController);