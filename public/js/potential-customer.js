Dropzone.autoDiscover = false;
var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker',
    // 'angularFileUpload'
    'thatisuday.dropzone'
  ]);

  function potentialCustomerController($scope, $http, $filter){
      // init the variables
      $scope.alldata = [];
      $scope.totalCount = 0;
      $scope.totalPages = 0;
      $scope.currentPage = 1;
      $scope.itemsPerPage = '100';
      $scope.indexFrom = 0;
      $scope.indexTo = 0;
      $scope.sortBy = true;
      $scope.sortName = '';
      $scope.today = moment().format("YYYY-MM-DD");
      $scope.search = {
          name: '',
          custcategory: '',
          account_manager: '',
          contact: '',
          created_at: '',
          created_at: '',
          pageNum: '100',
      }
      $scope.form = getDefaultForm()
      $scope.images = [];
      $scope.salesProgresses = [];

      // init page load
      getPage(1, true);
      getSalesProgress();

      angular.element(document).ready(function () {
          $('.select').select2({
            placeholder: 'Select...'
          });
          $('.selectmultiple').select2({
              placeholder: 'Choose one or many..'
          });
          $('#checkAll').change(function(){
              var all = this;
              $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
          });
      });

      $scope.exportData = function () {
          var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
              type: "application/vnd.ms-excel;charset=charset=utf-8"
          });
          var now = Date.now();
          saveAs(blob, "Potential Customer"+ now + ".xls");
      };

      // switching page
      $scope.pageChanged = function(newPage){
          getPage(newPage, false);
      };

      $scope.imagePageChanged = function(newPage){
        getImagePage(newPage, $scope.form.id);
      };

      $scope.pageNumChanged = function(){
          $scope.search['pageNum'] = $scope.itemsPerPage
          $scope.currentPage = 1
          getPage(1, false)
      };

      $scope.sortTable = function(sortName) {
          $scope.search.sortName = sortName;
          $scope.search.sortBy = ! $scope.search.sortBy;
          getPage(1);
      }

        // when hitting search button
      $scope.searchDB = function(){
          $scope.sortName = '';
          $scope.sortBy = '';
          getPage(1, false);
      }

      // on date changed
      $scope.onDateChange = function(scope_from, date){
          if(date){
              $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
          }
      }

      // checkbox all
      $scope.onCheckAllChecked = function() {
          var checked = $scope.checkall;

          $scope.alldata.forEach(function (transaction, key) {
              $scope.alldata[key].check = checked;
          });
      }
      $scope.files = [];
      let images = [];
      var formData = new FormData();

      $scope.uploadFile = function (potential_customer_id) {
          var request = {
              method: 'POST',
              url: '/api/potential-customer-attachment/potential-customer/' + potential_customer_id,
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
                  var fileElement = angular.element('#image_file');
                  fileElement.value = '';
                  getPage(1, false)
                  alert("Image(s) has been uploaded successfully!");
              }, function error(e) {
                  $scope.errors = e.data.errors;
              });
      };

      $scope.setTheFiles = function ($files) {
          angular.forEach($files, function (value, key) {
            //   console.log(value)
              formData.append('images[]', value);
          });
      };

      $scope.deleteFile = function(potential_customer_id) {
          $http.delete('/api/potential-customer-attachment/potential_customer/'+ potential_customer_id + '/delete').success(function(data) {

            //   $http.get('/api/bomcategory/' + bomcategory_id).success(function(catdata) {
            //       fetchSingleBomcategory(catdata);
            //   });
          });
      }

      $scope.onImageClicked = function(id, data = null) {
        if(!$scope.form.id) {
            $scope.onSingleEntryEdit(data)
        }
        getImagePage(1, id)
      }

      // upon form submit
      $scope.onFormSubmitClicked = function() {
        $http.post('/api/potential-customer/store-update', $scope.form).success(function(data) {
          $scope.form = getDefaultForm()
          $('.select').select2({
            placeholder: 'Select...'
          });
          getPage(1)
        });
      }

      $scope.onAddPotentialCustomerTemplateButtonClicked = function() {
        $scope.form = getDefaultForm()
      }

        //   on edit single entry
        $scope.onSingleEntryEdit = function(data) {
            let salesProgresses = [];
            if(data.sales_progresses) {
                angular.forEach(data.sales_progresses, function(value, index) {
                    salesProgresses[value.id] = true
                })
            }
            $scope.form = {
                id: data.id,
                name: data.name,
                custcategory_id: data.custcategory_id,
                account_manager_id: data.account_manager_id,
                attn_to: data.attn_to,
                contact: data.contact,
                address: data.address,
                postcode: data.postcode,
                remarks: data.remarks,
                is_important: data.is_important,
                salesProgresses: salesProgresses,
                attachments: data.potential_customer_attachments
            }
            $('.select').select2({
              placeholder: 'Select...'
            });

            $scope.attachmentOptions = {
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url : '/api/potential-customer-file',
              acceptedFiles : 'image/*',
              paramName: $scope.form.id,
            };
        }

      // on route template removed
      $scope.onSingleEntryRemoved = function(id) {
        let isConfirmRemove = confirm('Are you sure you want to remove the potential customer?');

        if(isConfirmRemove) {
          $http.delete('/api/potential-customer/delete/' + id).success(function(data) {
            getPage(1);
          })
        }else {
          return false;
        }
      }

      $scope.onPrevSingleClicked = function(scope_name, date) {
          $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
          $scope.searchDB();
      }

      $scope.onNextSingleClicked = function(scope_name, date) {
          $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
          $scope.searchDB();
      }

      $scope.dateChange = function(scope_from, date){
          if(date){
              $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
              // $scope.compareDateChange(scope_from);
          }
          $scope.searchDB();
      }

      $scope.onPrevImageClicked = function() {
        getImagePage($scope.images.currentPage - 1, $scope.form.id)
      }

      $scope.onNextImageClicked = function() {
        //   console.log($scope.images.currentPage, $scope.form.id)
        getImagePage($scope.images.currentPage + 1, $scope.form.id)
      }

      $scope.onRemoveImageClicked = function() {
        //   console.log($scope.images.alldata.data[0].id)
          $http.post('/api/potential-customer-attachment/' + $scope.images.alldata.data[0].id + '/delete').success(function(data) {
            getImagePage(1, $scope.form.id)
          })
      }

      $scope.syncSalesProgressCheck = function(itemArr, matchId) {
            let itemId = $filter('filter')(itemArr, {id: matchId })[0];
            if(itemId) {
                return true
            }else {
                return false
            }
      }

      $scope.onSalesProgressChanged = function(value) {
          console.log(value);
      }

      $scope.rolesObjFromArray = function (rolesArr) {
        rolesArr.forEach(function (role) {
          $scope.currentUser.rolesObj[role] = true;
        });
      }

      function getDefaultForm() {
        return {
          id: '',
          name: '',
          custcategory_id: '',
          account_manager_id: '',
          attn_to: '',
          contact: '',
          address: '',
          postcode: '',
          remarks: '',
          is_important: '',
          salesProgresses: [],
          checkboxes: []
        }
      }

      $scope.onMapClicked = function(data) {
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
        // console.log(JSON.parse(JSON.stringify(data)));
        if(data) {
            var contentString = '<span style=font-size:10px;>' +
                '<b>' +
                '(' + data.custcategory.name + ') ' + data.name + ' - ' + data.postcode +
                '</b>' +
                '</span>';

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            $http.get('https://www.onemap.gov.sg/api/common/elastic/search?searchVal=' + data.postcode + '&returnGeom=Y&getAddrDetails=Y').success(function(res) {
                    let lat = res.results[0].LATITUDE;
                    let lng = res.results[0].LONGITUDE;

                    let url = map_icon_base + MAP_ICON_FILE[data.custcategory.map_icon_file]
                    // console.log(JSON.parse(JSON.stringify(data)))
                    let pos = new google.maps.LatLng(lat, lng);

                    let marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: '(' + data.custcategory.name + ') ' + data.name + ' - ' + data.postcode,
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

      // retrieve page w/wo search
      function getPage(pageNumber, first){
          $scope.spinner = true;
          $http.post('/api/potential-customer?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
              if(data.data.data){
                  $scope.alldata = data.data.data;
                  $scope.totalCount = data.data.total;
                  $scope.currentPage = data.data.current_page;
                  $scope.indexFrom = data.data.from;
                  $scope.indexTo = data.data.to;
              }else{
                  $scope.alldata = data.data;
                  $scope.totalCount = data.data.length;
                  $scope.currentPage = 1;
                  $scope.indexFrom = 1;
                  $scope.indexTo = data.data.length;
              }
              // get total count
              $scope.All = data.data.length;

              // return total amount
              $scope.spinner = false;
          });
      }

      function getSalesProgress() {
          $http.post('/api/sales-progress?page="All"').success(function(data) {
            $scope.salesProgresses = data.data;
          })
      }

      function getImagePage(pageNumber, id) {
        $http.post('/api/potential-customer/'+ id + '/attachments?page=' + pageNumber).success(function(data) {
            $scope.images.alldata = data.data;
            $scope.images.totalCount = data.data.data.length;
            $scope.images.currentPage = data.data.current_page;
            $scope.images.itemsPerPage = 1;
            $scope.images.indexFrom = 1;
            $scope.images.indexTo = data.data.data.length;
        });
      }
  }

  function performanceController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = '100';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        current_month: moment().month()+1 + '-' + moment().year(),
        account_manager_id: '',
        pageNum: '100',
    }

    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2({
          placeholder: 'Select...'
        });
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
        $('#checkAll').change(function(){
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Potential Cust Performance"+ now + ".xls");
    };

    // switching page
    $scope.pageChanged = function(newPage){
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function(){
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1, false)
    };

    $scope.sortTable = function(sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = ! $scope.search.sortBy;
        getPage(1);
    }

      // when hitting search button
    $scope.searchDB = function(){
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // on date changed
    $scope.onDateChange = function(scope_from, date){
        if(date){
            $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

    // checkbox all
    $scope.onCheckAllChecked = function() {
        var checked = $scope.checkall;

        $scope.alldata.forEach(function (transaction, key) {
            $scope.alldata[key].check = checked;
        });
    }

    $scope.onPrevSingleClicked = function(scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextSingleClicked = function(scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.dateChange = function(scope_from, date){
        if(date){
            $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
            // $scope.compareDateChange(scope_from);
        }
        $scope.searchDB();
    }

    $scope.formDateChange = function(scope_from, date){
      if(date){
          $scope.form[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
          // $scope.compareDateChange(scope_from);
      }
  }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/potential-customer/performance?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            $scope.data = data;

            // if(data.data.data){
            //     $scope.alldata = data.data.data;
            //     $scope.totalCount = data.data.total;
            //     $scope.currentPage = data.data.current_page;
            //     $scope.indexFrom = data.data.from;
            //     $scope.indexTo = data.data.to;
            // }else{
            //     $scope.alldata = data.data;
            //     $scope.totalCount = data.data.length;
            //     $scope.currentPage = 1;
            //     $scope.indexFrom = 1;
            //     $scope.indexTo = data.data.length;
            // }
            // $scope.All = data.data.length;

            // return total amount
            $scope.spinner = false;
        });
    }
  }

  function meetingMinuteController($scope, $http){
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 'All';
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.sortBy = true;
    $scope.sortName = '';
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.search = {
        date: '',
        details: '',
        created_at: '',
        created_at: '',
        pageNum: 'All',
    }
    $scope.form = getDefaultForm()

    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2({
          placeholder: 'Select...'
        });
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
        $('#checkAll').change(function(){
            var all = this;
            $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Meeting Minutes"+ now + ".xls");
    };

    // switching page
    $scope.pageChanged = function(newPage){
        getPage(newPage, false);
    };

    $scope.pageNumChanged = function(){
        $scope.search['pageNum'] = $scope.itemsPerPage
        $scope.currentPage = 1
        getPage(1, false)
    };

    $scope.sortTable = function(sortName) {
        $scope.search.sortName = sortName;
        $scope.search.sortBy = ! $scope.search.sortBy;
        getPage(1);
    }

      // when hitting search button
    $scope.searchDB = function(){
        $scope.sortName = '';
        $scope.sortBy = '';
        getPage(1, false);
    }

    // on date changed
    $scope.onDateChange = function(scope_from, date){
        if(date){
            $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

    // checkbox all
    $scope.onCheckAllChecked = function() {
        var checked = $scope.checkall;

        $scope.alldata.forEach(function (transaction, key) {
            $scope.alldata[key].check = checked;
        });
    }

  //   $scope.merchandiserInit = function(userId) {
  //       $scope.search.account_manager = userId;
  //   }


    // delete single entry api
    $scope.onSingleEntryDeleted = function(item) {
      let index = $scope.form.route_template_items.indexOf(item);
      $scope.form.route_template_items.splice(index, 1)
    }

    // upon form submit
    $scope.onFormSubmitClicked = function() {
      $http.post('/api/meeting-minute/store-update', $scope.form).success(function(data) {
        $scope.form = getDefaultForm()
        $('.select').select2({
          placeholder: 'Select...'
        });
        getPage(1)
      });
    }

      //   on edit single entry
      $scope.onSingleEntryEdit = function(data) {
          $scope.form = {
              id: data.id,
              date: data.date,
              details: data.details,
          }
          $('.select').select2({
            placeholder: 'Select...'
          });
      }

    // on route template removed
    $scope.onSingleEntryRemoved = function(id) {
      let isConfirmRemove = confirm('Are you sure you want to remove the meeting minutes?');

      if(isConfirmRemove) {
        $http.delete('/api/meeting-minute/delete/' + id).success(function(data) {
          getPage(1);
        })
      }else {
        return false;
      }
    }

    $scope.onPrevSingleClicked = function(scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.onNextSingleClicked = function(scope_name, date) {
        $scope.search[scope_name] = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        $scope.searchDB();
    }

    $scope.dateChange = function(scope_from, date){
        if(date){
            $scope.search[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
            // $scope.compareDateChange(scope_from);
        }
        $scope.searchDB();
    }

    $scope.formDateChange = function(scope_from, date){
      if(date){
          $scope.form[scope_from] = moment(new Date(date)).format('YYYY-MM-DD');
          // $scope.compareDateChange(scope_from);
      }
  }

    function getDefaultForm() {
      return {
        id: '',
        date: '',
        details: '',
      }
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/meeting-minute?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.data.data){
                $scope.alldata = data.data.data;
                $scope.totalCount = data.data.total;
                $scope.currentPage = data.data.current_page;
                $scope.indexFrom = data.data.from;
                $scope.indexTo = data.data.to;
            }else{
                $scope.alldata = data.data;
                $scope.totalCount = data.data.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.data.length;
            }
            // get total count
            $scope.All = data.data.length;

            // return total amount
            $scope.spinner = false;
        });
    }
  }

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


  app.controller('potentialCustomerController', potentialCustomerController);
  app.controller('performanceController', performanceController);
  app.controller('meetingMinuteController', meetingMinuteController);