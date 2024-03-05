var app = angular.module('app', [
  'angularUtils.directives.dirPagination',
  'ui.select',
  'ngSanitize',
  '720kb.datepicker'
]);

function performanceOfficeIndexController($scope, $http) {
  // init the variables
  $scope.alldata = [];
  $scope.totalCount = 0;
  $scope.totalPages = 0;
  $scope.currentPage = 1;
  $scope.indexFrom = 0;
  $scope.indexTo = 0;
  $scope.search = {
      vend_id: '',
      cust_id: '',
      company: '',
      custcategory: '',
      serial_no: '',
      racking_config_id: '',
      vend_code: '',
      itemsPerPage: 100,
      sortName: '',
      sortBy: true
  }

  // init page load
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
      saveAs(blob, "VMRpt" + now + ".xls");
  };

  // switching page
  $scope.pageChanged = function (newPage) {
      getPage(newPage);
  };

  $scope.pageNumChanged = function () {
      $scope.search['pageNum'] = $scope.itemsPerPage
      $scope.currentPage = 1
      getPage(1)
  };

  $scope.sortTable = function (sortName) {
      $scope.search.sortName = sortName;
      $scope.search.sortBy = !$scope.search.sortBy;
      getPage(1);
  }

  // when hitting search button
  $scope.searchDB = function () {
      $scope.search.sortName = '';
      $scope.search.sortBy = true;
      getPage(1);
  }

  // retrieve page w/wo search
  function getPage(pageNumber) {
      $scope.spinner = true;
      $http.post('/api/performance/office?page=' + pageNumber, $scope.search).success(function (data) {
          if (data.model.data) {
              $scope.alldata = data.model.data;
              $scope.totalCount = data.model.total;
              $scope.currentPage = data.model.current_page;
              $scope.indexFrom = data.model.from;
              $scope.indexTo = data.model.to;
          } else {
              $scope.alldata = data.model;
              $scope.totalCount = data.model.length;
              $scope.currentPage = 1;
              $scope.indexFrom = 1;
              $scope.indexTo = data.model.length;
          }
          // get total count
          $scope.All = data.model.length;

          // return total amount
          $scope.total_amount = data.total_amount;
          $scope.spinner = false;
      }).error(function (data) {

      });
  }

  //delete record
  $scope.confirmDelete = function (event, id) {
      var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
      if (isConfirmDelete) {
          $http({
              method: 'DELETE',
              url: '/vm/' + id + '/destroy'
          }).success(function (data) {
              location.reload();
          }).error(function (data) {
              alert('Unable to delete');
          })
      } else {
          return false;
      }
  }
}

app.filter('delDate', [
  '$filter', function ($filter) {
      return function (input, format) {
          return $filter('date')(new Date(input), format);
      };
  }
]);

app.controller('performanceOfficeIndexController', performanceOfficeIndexController);