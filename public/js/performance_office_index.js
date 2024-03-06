var app = angular.module('app', [
  'angularUtils.directives.dirPagination',
  'ui.select',
  'ngSanitize',
  '720kb.datepicker'
]);

function performanceOfficeIndexController($scope, $http) {
  // init the variables
  $scope.today = moment().format("YYYY-MM-DD");
  $scope.alldata = [];
  $scope.totalCount = 0;
  $scope.totalPages = 0;
  $scope.currentPage = 1;
  $scope.indexFrom = 0;
  $scope.indexTo = 0;
  $scope.search = {
      date: $scope.today,
      name: '',
      status: '',
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
        $scope.headers = data.headers;
        $scope.contents = data.contents;
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