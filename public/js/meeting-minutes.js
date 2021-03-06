var app = angular.module('app', [
    'angularUtils.directives.dirPagination',
    'ui.select',
    'ngSanitize',
    '720kb.datepicker'
  ]);

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

  app.controller('meetingMinuteController', meetingMinuteController);
