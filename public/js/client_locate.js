var app = angular.module('app', [
  'angularUtils.directives.dirPagination',
  'ui.select',
  'ngSanitize',
  '720kb.datepicker'
]);

function personController($scope, $http){
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
      contact: '',
      active: ['Yes'],
      account_manager: '',
      zone_id: '',
      pageNum: 'All',
      profile_id: '',
      franchisee_id: '',
      excludeCustCat: ''
  }
  $scope.assignForm = {
      name: '',
      custcategory: '',
      account_manager: '',
      zone_id: '',
      tag_id: '',
      detach: '',
      driver: '',
      delivery_date: $scope.today
  }
  // init page load
  getPage(1, true);

  $scope.onMapClicked = function(singleperson = null, index = null, type = null) {
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
            // '<br>' +
            // '<span style="font-size:13px">' + '<b>' + singleperson.del_postcode + '</b>' + '</span>' + ' ' + singleperson.del_address +
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
            $http.post('/api/person/storelatlng/' + singleperson.id, coord).success(function (data) {
                $scope.alldata[index].del_lat = data.del_lat;
                $scope.alldata[index].del_lng = data.del_lng;

                let url = map_icon_base + MAP_ICON_FILE[singleperson.map_icon_file]
                var pos = new google.maps.LatLng(singleperson.del_lat, singleperson.del_lng);
                if(type === 2) {
                  var marker = new google.maps.Marker({
                      position: pos,
                      map: map,
                      title: singleperson.cust_id + ' - ' + singleperson.company + ' - ' + singleperson.custcategory,
                      label: {fontSize: '13px', text: '(' + (key + $scope.indexFrom).toString() + ')' + custString, fontWeight: 'bold'},
                      icon: {
                          labelOrigin: new google.maps.Point(15,10),
                          url: url
                      }
                  });
                }else {
                  var marker = new google.maps.Marker({
                      position: pos,
                      map: map,
                      title: singleperson.cust_id + ' - ' + singleperson.company + ' - ' + singleperson.custcategory,
                      label: {fontSize: '15px', text: (key + $scope.indexFrom).toString(), fontWeight: 'bold'},
                      icon: {
                          labelOrigin: new google.maps.Point(15,10),
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

    }else {
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
            if(!person.del_lat && !person.del_lng) {
                $http.get('https://developers.onemap.sg/commonapi/search?searchVal=' + person.del_postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(data) {
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
            if(type === 2) {
              var marker = new google.maps.Marker({
                  position: pos,
                  map: map,
                  title: person.cust_id + ' - ' + person.company + ' - ' + person.custcategory,
                  label: {fontSize: '13px', text: '(' + (key + $scope.indexFrom).toString() + ')' + custString, fontWeight: 'bold'},
                  icon: {
                      labelOrigin: new google.maps.Point(15,10),
                      url: url
                  }
              });
            }else {
              var marker = new google.maps.Marker({
                  position: pos,
                  map: map,
                  title: person.cust_id + ' - ' + person.company + ' - ' + person.custcategory,
                  label: {fontSize: '15px', text: (key + $scope.indexFrom).toString(), fontWeight: 'bold'},
                  icon: {
                      labelOrigin: new google.maps.Point(15,10),
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
  function getFvmApi(pageNumber, first){
      $scope.spinner = true;
      $http.get('/api/client/locate/fvm').success(function(data){
          $scope.fvmPeople = data;
          // return total amount
          $scope.spinner = false;
      });
  }
}


app.controller('personController', personController);
