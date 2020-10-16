var app = angular.module('app', [
  'angularUtils.directives.dirPagination',
  'ui.select',
  'ngSanitize',
  '720kb.datepicker'
]);

function locateController($scope, $http){
  // init the variables
  $scope.people = [];

  getVendingApi('fvmMap');
  getVendingApi('dvmMap');

  function loadPeopleMap(people, mapId) {
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

    var map = new google.maps.Map(document.getElementById(mapId), {
        center: locationLatLng,
        zoom: 12
    });

    var geocoder = new google.maps.Geocoder();

    var markers = [];
    $scope.coordsArr = [];
    people.forEach(function (person, key) {
          let custString = person.cust_id + ' - ' + person.company;
        var contentString = '<span style=font-size:10px;>' +
            '<b>' +
            custString +
            '</b>' +
            '</span>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
/*
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
        } */

        var pos = new google.maps.LatLng(person.del_lat, person.del_lng);
        var marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: person.cust_id + ' - ' + person.company + ' - ' + person.custcategory,
            label: {fontSize: '13px', text: '(' + (key + $scope.indexFrom).toString() + ')' + custString, fontWeight: 'bold'}
        });

        markers.push(marker);
        infowindow.open(map, marker);

        marker.addListener('click', function () {
            infowindow.open(map, marker);
        });

        google.maps.event.trigger(map, "resize");
        map.setCenter(locationLatLng);
    });

  }


  function getVendingApi(vendingType = 'fvm'){
      $scope.spinner = true;
      $http.post('/api/client/locate/' + vendingType).success(function(data){
          $scope.people = data

          loadPeopleMap($scope.people, vendingType);
          $scope.spinner = false;
      });
  }
}


app.controller('locateController', locateController);
