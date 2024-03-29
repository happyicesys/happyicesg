var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
]);

function personController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.datasetTemp = {};
    $scope.totalCountTemp = {};
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.headerTemp = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.showBatchFunctionPanel = false;
    $scope.checkall = '';
    $scope.search = {
        cust_id: '',
        strictCustId: '',
        custcategory: '',
        company: '',
        contact: '',
        active: ['Yes'],
        account_manager: '',
        zone_id: '',
        pageNum: 'All',
        profile_id: '',
        franchisee_id: '',
        excludeCustCat: '',
        freezers: '',
        priceTemplates: [],
        edited: false,
        updated_by: '',
        is_pwp: '',
        created_from: '',
        created_to: '',
        del_address: '',
        prefix_code: '',
        code: '',
        vend_code: '',
    }
    $scope.assignForm = {
        name: '',
        locationType: '',
        custcategory: '',
        account_manager: '',
        zone_id: '',
        tag_id: '',
        price_template_id: '',
        detach: '',
        detach_price_template: '',
        driver: '',
        delivery_date: $scope.today,
        transremark: '',
        is_service: 'false'
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
        $('#checkAll').change(function () {
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_people_list').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Rpt" + now + ".xls");
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

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1);
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    // when hitting search button
    // $scope.searchDB = function (event) {
    // event.preventDefault();
    // $scope.search.edited = true;
    // if (event.keyCode && event.keyCode === 13) {
    //     $scope.search.sortName = '';
    //     $scope.search.sortBy = true;
    // }
    // getPage(1, false);
    // }

    // retrieve franchisee id
    $scope.getFranchiseeId = function () {
        $http.get('/api/franchisee/auth').success(function (data) {
            return data;
        });
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    // batch function button dropdown
    $scope.onBatchFunctionClicked = function (event) {
        event.preventDefault();
        $scope.showBatchFunctionPanel = !$scope.showBatchFunctionPanel;
    }

    // checkbox all
    $scope.onCheckAllChecked = function () {
        var checked = $scope.checkall;

        $scope.alldata.forEach(function (transaction, key) {
            $scope.alldata[key].check = checked;
        });
    }

    // quick batch assign
    $scope.onBatchAssignClicked = function (event, assignName) {
        event.preventDefault();
        $scope.assignForm.name = assignName;
        $http.post('/api/person/batch-update', { people: $scope.alldata, assignForm: $scope.assignForm }).success(function (data) {
            $scope.checkall = false;
            if (data.transactions.length > 0) {
                alert('Invoices ' + data.transactions + ' created');
            }else {
                alert('Entries updated');
            }
            location.reload();
        })
    }

    $scope.formDateChange = function (scope, date) {
        if (date) {
            $scope.assignForm[scope] = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

    $scope.dateChange = function (scope_from, date) {
        if (date) {
            $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.onPrevSingleClicked = function (scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextSingleClicked = function (scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onMapClicked = function (singleperson = null, index = null, type = null) {
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
            var contentString = '<span style=font-size:10px;>' +
                '<b>' +
                '(' + singleperson.id + ') ' + singleperson.code + ' - ' + singleperson.company +
                '</b>' +
                // '<br>' +
                // '<span style="font-size:13px">' + '<b>' + singleperson.del_postcode + '</b>' + '</span>' + ' ' + singleperson.del_address +
                '</span>' +
                '<br>' +
                '<br>' +
                '<span style="font-size:14px">' + '<b>' + '<a target="_blank" href="https://maps.google.com/?q=' + singleperson.del_lat + ',' + singleperson.del_lng + '">' + 'View on Google Map' + '</a>' + '</b>' + '</span>';

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            $http.get('https://www.onemap.gov.sg/api/common/elastic/search?searchVal=' + singleperson.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function (data) {
                let coord = {
                    transaction_id: singleperson.id,
                    lat: data.results[0].LATITUDE,
                    lng: data.results[0].LONGITUDE,
                }
                $http.post('/api/person/storelatlng/' + singleperson.id, coord).success(function (data) {
                    $scope.alldata[index].del_lat = data.del_lat;
                    $scope.alldata[index].del_lng = data.del_lng;

                    let url = map_icon_base + MAP_ICON_FILE[singleperson.map_icon_file]
                    var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                    if (type === 2) {
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: singleperson.code + ' - ' + singleperson.company + ' - ' + singleperson.custcategory,
                            label: { fontSize: '13px', text: '(' + singleperson.code + ') ' + singleperson.company, fontWeight: 'bold' },
                            icon: {
                                labelOrigin: new google.maps.Point(15, 10),
                                url: url
                            }
                        });
                    } else {
                        var marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: singleperson.code + ' - ' + singleperson.company + ' - ' + singleperson.custcategory,
                            label: { fontSize: '15px', text: '(' + singleperson.code + ') ' + singleperson.company, fontWeight: 'bold' },
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

                if (!person.del_lat && !person.del_lng) {
                    $http.get('https://www.onemap.gov.sg/api/common/elastic/search?searchVal=' + person.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function (data) {
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

                let custString = person.code + ' - ' + person.company + ' - ' + person.custcategory;
                var contentString = '<span style=font-size:10px;>' +
                    '<b>' +
                    custString +
                    '</b>' +
                    // '<br>' +
                    // '<span style="font-size:13px">' + '<b>' + person.del_postcode + '</b>' + '</span>' + ' ' + person.del_address +
                    '</span>' +
                    '<br>' +
                    '<br>' +
                    '<span style="font-size:14px">' + '<b>' + '<a target="_blank" href="https://maps.google.com/?q=' + person.del_lat + ',' + person.del_lng + '">' + 'View on Google Map' + '</a>' + '</b>' + '</span>';

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                let url = map_icon_base + MAP_ICON_FILE[person.map_icon_file]
                var pos = new google.maps.LatLng(person.del_lat, person.del_lng);
                if (type === 2) {
                    var marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: person.code + ' - ' + person.company + ' - ' + person.custcategory,
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
                        title: person.code + ' - ' + person.company + ' - ' + person.custcategory,
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

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/people?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.people.data) {
                $scope.alldata = data.people.data;
                $scope.totalCount = data.people.total;
                $scope.currentPage = data.people.current_page;
                $scope.indexFrom = data.people.from;
                $scope.indexTo = data.people.to;
            } else {
                $scope.alldata = data.people;
                $scope.totalCount = data.people.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.people.length;
            }
            // get total count
            $scope.All = data.people.length;

            // return total amount
            $scope.spinner = false;
            $scope.search.edited = false;
        });
    }
}


function creationController($scope, $http) {
    // init the variables
    $scope.alldata = [];
    $scope.datasetTemp = {};
    $scope.totalCountTemp = {};
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.headerTemp = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.showBatchFunctionPanel = false;
    $scope.search = {
        cust_id: '',
        strictCustId: '',
        custcategory: '',
        company: '',
        active: ['Yes'],
        account_manager: '',
        pageNum: 'All',
        profile_id: '',
        excludeCustCat: '',
        edited: false,
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
        $('#checkAll').change(function () {
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Customer Creation Rpt" + now + ".xls");
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

    $scope.sortTable = function (sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = !$scope.search.sortBy;
        getPage(1);
    }

    // search button transaction index
    $scope.onSearchButtonClicked = function (event) {
        event.preventDefault();
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    // when hitting search button
    $scope.searchDB = function () {
        $scope.search.edited = true;
        $scope.search.sortName = '';
        $scope.search.sortBy = true;
        getPage(1, false);
    }

    $scope.merchandiserInit = function (userId) {
        $scope.search.account_manager = userId;
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first) {

        $scope.spinner = true;
        $http.post('/api/person/creation?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {

            $scope.alldata = data;
            // console.log(JSON.parse(JSON.stringify($scope.alldata)))
            // return total amount
            $scope.spinner = false;
            $scope.search.edited = false;
        });
    }
}

app.controller('personController', personController);
app.controller('creationController', creationController);
