var app = angular.module('app', [
  'ngSanitize',
  'ui.select',
  'angularUtils.directives.dirPagination',
  '720kb.datepicker',
], ['$httpProvider', function ($httpProvider) {
  $httpProvider.defaults.headers.post['X-CSRF-TOKEN'] = $('meta[name=csrf-token]').attr('content');
}]);


function personassetCategoryController($scope, $http, $window) {
  // init the variables
  $scope.alldata = [];
  $scope.totalCount = 0;
  $scope.totalPages = 0;
  $scope.currentPage = 1;
  $scope.itemsPerPage = 100;
  $scope.indexFrom = 0;
  $scope.indexTo = 0;
  $scope.search = {
    person_id: '',
    code: '',
    name: '',
    brand: '',
    pageNum: 100,
    sortName: '',
    sortBy: true
  }
  $scope.form = {
    title: '',
    person_id: '',
    remarks: '',
    complete_date: '',
    is_refund: '',
    refund_name: '',
    refund_bank: '',
    refund_account: '',
    refund_contact: '',
    vending_details: '',
    vending_id: '',
    error_code: '',
    lane_number: ''
  }

  // init page load
  getPage(1, true);
  fetchPeopleApi();

  angular.element(document).ready(function () {
    $('.select2').select2();
  });

  $scope.exportData = function () {
    var blob = new Blob(["\ufeff", document.getElementById('exportable_personasset').innerHTML], {
      type: "application/vnd.ms-excel;charset=charset=utf-8"
    });
    var now = Date.now();
    saveAs(blob, "Customer Asset" + now + ".xls");
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
    getPage(1, false);
  }

  $scope.createdFromChange = function (date) {
    if (date) {
      $scope.search.created_from = moment(new Date(date)).format('YYYY-MM-DD');
    }
    $scope.searchDB();
  }

  $scope.createdToChange = function (date) {
    if (date) {
      $scope.search.created_to = moment(new Date(date)).format('YYYY-MM-DD');
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

  // when hitting search button
  $scope.searchDB = function () {
    $scope.search.sortName = '';
    $scope.search.sortBy = true;
    getPage(1, false);
  }

  $scope.onSelected = function (selectedItem) {
    if (selectedItem.vending) {
      $scope.form.vending_details = selectedItem.vending.serial_no + ' - ' + selectedItem.vending.type;
      $scope.form.vending_id = selectedItem.vending.id;
    } else {
      $scope.form.vending_details = '';
      $scope.form.vending_id = '';
    }
  }

  // retrieve page w/wo search
  function getPage(pageNumber, first) {
    $scope.spinner = true;
    $http.post('/api/personassets?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
      if (data.data.data) {
        $scope.alldata = data.data.data;
        $scope.totalCount = data.data.total;
        $scope.currentPage = data.data.current_page;
        $scope.indexFrom = data.data.from;
        $scope.indexTo = data.data.to;
      } else {
        $scope.alldata = data.data;
        $scope.totalCount = data.data.length;
        $scope.currentPage = 1;
        $scope.indexFrom = 1;
        $scope.indexTo = data.data.length;
      }
      $scope.All = data.data.length;
      $scope.spinner = false;
    });
  }

  function clearForm() {
    $scope.form = {
      id: '',
      code: '',
      name: '',
      brand: '',
      size1: '',
      size2: '',
      weight: '',
      capacity: '',
      specs1: '',
      specs2: '',
      specs3: '',
      person_id: ''
    }
  }

  $scope.createPersonassetModal = function () {
    clearForm();
  }

  $scope.createPersonasset = function () {
    $http.post('/api/personasset/create', $scope.form).success(function (data) {
      getPage(1);
      clearForm();
    }).error(function (data, status) {
      $scope.formErrors = data;
    });
  }

  $scope.removeEntry = function (id) {
    var isConfirmDelete = confirm('Are you sure to DELETE this customer asset?');
    if (isConfirmDelete) {
      $http.delete('/api/personasset/' + id + '/delete').success(function (data) {
        getPage(1);
      });
    } else {
      return false;
    }
  }

  $scope.editPersonassetModal = function (personasset) {
    fetchSinglePersonasset(personasset);
  }

  function fetchSinglePersonasset(data) {
    $scope.form = {
      id: data.id,
      code: data.code,
      name: data.name,
      brand: data.brand,
      size1: data.size1,
      size2: data.size2,
      weight: data.weight,
      capacity: data.capacity,
      specs1: data.specs1,
      specs2: data.specs2,
      specs3: data.specs3,
      person_id: data.person_id,
    }
  }

  function fetchPeopleApi() {
    $http.get('/api/people/options').success(function (data) {
      $scope.people = data;
    });
  }

  $scope.updatePersonasset = function () {
    $http.post('/api/personasset/update', $scope.form).success(function (data) {
      getPage(1);
    });
  }

  $scope.isFormValid = function () {
    if ($scope.form.code && $scope.form.name && $scope.form.person_id) {
      return false;
    } else {
      return true;
    }
  }
}

function personassetMovementController($scope, $http, $window) {
  // init the variables
  $scope.alldata = [];
  $scope.totalCount = 0;
  $scope.totalPages = 0;
  $scope.currentPage = 1;
  $scope.itemsPerPage = 100;
  $scope.indexFrom = 0;
  $scope.indexTo = 0;
  $scope.thisyear = moment().format('YYYY');
  $scope.thisweek = moment().isoWeekday(1).format('w');
  $scope.lasttwoyears = [
    $scope.thisyear,
    moment().subtract(1, 'years').format('YYYY'),
    moment().subtract(2, 'years').format('YYYY'),
  ];
  $scope.allweeks = [];
  $scope.search = {
    year: $scope.thisyear,
    week: $scope.thisweek,
    datefrom: moment().year($scope.thisyear).week($scope.thisweek).startOf('isoWeek').format('YYYY-MM-DD'),
    dateto: moment().year($scope.thisyear).week($scope.thisweek).endOf('isoWeek').format('YYYY-MM-DD'),
    code: '',
    name: '',
    brand: '',
    from_location: '',
    from_invoice: '',
    to_location: '',
    to_invoice: '',
    serial_no: '',
    sticker: '',
    comment: '',
    pageNum: 100,
    sortName: '',
    sortBy: true
  }
  $scope.form = {
    title: '',
    person_id: '',
    remarks: '',
    complete_date: '',
    is_refund: '',
    refund_name: '',
    refund_bank: '',
    refund_account: '',
    refund_contact: '',
    vending_details: '',
    vending_id: '',
    error_code: '',
    lane_number: ''
  }

  // init page load
  getAllWeekNumbers();
  getPage(1, true);
  fetchPeopleApi();

  angular.element(document).ready(function () {
    $('.select2').select2();
  });

  $scope.exportData = function () {
    var blob = new Blob(["\ufeff", document.getElementById('exportable_personassetmovement').innerHTML], {
      type: "application/vnd.ms-excel;charset=charset=utf-8"
    });
    var now = Date.now();
    saveAs(blob, "Customer Asset Movement" + now + ".xls");
  };


  $scope.dateFromChange = function (date) {
    if (date) {
      $scope.search.datefrom = moment(new Date(date)).format('YYYY-MM-DD');
    }
    $scope.searchDB();
  }

  $scope.dateToChange = function (date) {
    if (date) {
      $scope.search.dateto = moment(new Date(date)).format('YYYY-MM-DD');
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
    getPage(1, false);
  }

  // when hitting search button
  $scope.searchDB = function () {
    // $scope.search.datefrom = moment().year($scope.search.year).week($scope.search.week).startOf('isoWeek').format('YYYY-MM-DD');
    // $scope.search.dateto = moment().year($scope.search.year).week($scope.search.week).endOf('isoWeek').format('YYYY-MM-DD');
    $scope.search.sortName = '';
    $scope.search.sortBy = true;
    getPage(1, false);
  }

  $scope.getWeekDifference = function(datein, dateout) {
    var delta = '';

    if(datein && dateout) {
      var datein = moment(datein, 'YYYY-MM-DD');
      var dateout = moment(dateout, 'YYYY-MM-DD');
      delta = datein.diff(dateout, 'week');
    }
    return delta;
  }

  function getAllWeekNumbers() {
    var i;
    for(i=1; i<=52; i++) {
      $scope.allweeks.push(i);
    }
  }

  // retrieve page w/wo search
  function getPage(pageNumber, first) {
    $scope.spinner = true;

    $http.post('/api/personassets?init=' + first, {pageNum: 'All'}).success(function (data) {
      $scope.personassetcategories = data.data;
    });

    $http.post('/api/personassetmovements?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
      if (data.data.data) {
        $scope.alldata = data.data.data;
        $scope.totalCount = data.data.total;
        $scope.currentPage = data.data.current_page;
        $scope.indexFrom = data.data.from;
        $scope.indexTo = data.data.to;
      } else {
        $scope.alldata = data.data;
        $scope.totalCount = data.data.length;
        $scope.currentPage = 1;
        $scope.indexFrom = 1;
        $scope.indexTo = data.data.length;
      }
      $scope.All = data.data.length;
      $scope.spinner = false;
    });
  }

  function clearForm() {
    $scope.form = {
      id: '',
      personasset_id: '',
      serial_no: '',
      sticker: '',
      comment: ''
    }
  }

  $scope.createPersonassetModal = function () {
    clearForm();
  }

  $scope.createPersonasset = function () {
    $http.post('/api/personasset/create', $scope.form).success(function (data) {
      getPage(1);
      clearForm();
    }).error(function (data, status) {
      $scope.formErrors = data;
    });
  }

  $scope.removeEntry = function (id) {
    var isConfirmDelete = confirm('Are you sure to DELETE this customer asset?');
    if (isConfirmDelete) {
      $http.delete('/api/transactionpersonasset/' + id + '/delete').success(function (data) {
        getPage(1);
      });
    } else {
      return false;
    }
  }

  $scope.editPersonassetMovementModal = function (personassetmovement) {
    fetchSinglePersonassetMovement(personassetmovement);
  }

  function fetchSinglePersonassetMovement(data) {
    $scope.form = {
      id: data.id,
      personasset_id: data.personasset_id,
      serial_no: data.serial_no,
      sticker: data.sticker,
      remarks: data.remarks
    }
  }

  function fetchPeopleApi() {
    $http.get('/api/people/options').success(function (data) {
      $scope.people = data;
    });
  }

  $scope.updatePersonassetMovement = function () {
    $http.post('/api/transactionpersonasset/update', $scope.form).success(function (data) {
      getPage(1);
    });
  }

  $scope.isFormValid = function () {
    if ($scope.form.serial_no && $scope.form.sticker && $scope.form.remarks) {
      return false;
    } else {
      return true;
    }
  }
}

function personassetCurrentController($scope, $http, $window) {
  // init the variables
  $scope.alldata = [];
  $scope.totalCount = 0;
  $scope.totalPages = 0;
  $scope.currentPage = 1;
  $scope.itemsPerPage = 100;
  $scope.indexFrom = 0;
  $scope.indexTo = 0;
  $scope.thisyear = moment().format('YYYY');
  $scope.thisweek = moment().isoWeekday(1).format('w');
  $scope.allweeks = [];
  $scope.search = {
    year: $scope.thisyear,
    week: $scope.thisweek,
    datefrom: '',
    dateto: '',
    code: '',
    name: '',
    brand: '',
    from_location: '',
    from_invoice: '',
    serial_no: '',
    sticker: '',
    comment: '',
    pageNum: 100,
    sortName: '',
    sortBy: true
  }
  $scope.form = {
    title: '',
    person_id: '',
    remarks: '',
    complete_date: '',
    is_refund: '',
    refund_name: '',
    refund_bank: '',
    refund_account: '',
    refund_contact: '',
    vending_details: '',
    vending_id: '',
    error_code: '',
    lane_number: ''
  }

  // init page load
  getAllWeekNumbers();
  getPage(1, true);
  fetchPeopleApi();

  angular.element(document).ready(function () {
    $('.select2').select2();
  });

  $scope.exportData = function () {
    var blob = new Blob(["\ufeff", document.getElementById('exportable_personassetcurrent').innerHTML], {
      type: "application/vnd.ms-excel;charset=charset=utf-8"
    });
    var now = Date.now();
    saveAs(blob, "Current Asset" + now + ".xls");
  };


  $scope.dateFromChange = function (date) {
    if (date) {
      $scope.search.datefrom = moment(new Date(date)).format('YYYY-MM-DD');
    }
    $scope.searchDB();
  }

  $scope.dateToChange = function (date) {
    if (date) {
      $scope.search.dateto = moment(new Date(date)).format('YYYY-MM-DD');
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
    getPage(1, false);
  }

  // when hitting search button
  $scope.searchDB = function () {
    // $scope.search.datefrom = moment().year($scope.search.year).week($scope.search.week).startOf('isoWeek').format('YYYY-MM-DD');
    // $scope.search.dateto = moment().year($scope.search.year).week($scope.search.week).endOf('isoWeek').format('YYYY-MM-DD');
    $scope.search.sortName = '';
    $scope.search.sortBy = true;
    getPage(1, false);
  }

  $scope.getWeekDifference = function (datein, dateout) {
    var delta = '';

    if (datein && dateout) {
      var datein = moment(datein, 'YYYY-MM-DD');
      var dateout = moment(dateout, 'YYYY-MM-DD');
      delta = datein.diff(dateout, 'week');
    }
    return delta;
  }

  function getAllWeekNumbers() {
    var i;
    for (i = 1; i <= 52; i++) {
      $scope.allweeks.push(i);
    }
  }

  // retrieve page w/wo search
  function getPage(pageNumber, first) {
    $scope.spinner = true;
    $http.post('/api/personassetcurrents?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
      if (data.data.data) {
        $scope.alldata = data.data.data;
        $scope.totalCount = data.data.total;
        $scope.currentPage = data.data.current_page;
        $scope.indexFrom = data.data.from;
        $scope.indexTo = data.data.to;
      } else {
        $scope.alldata = data.data;
        $scope.totalCount = data.data.length;
        $scope.currentPage = 1;
        $scope.indexFrom = 1;
        $scope.indexTo = data.data.length;
      }
      $scope.All = data.data.length;
      $scope.spinner = false;
    });
  }

  function clearForm() {
    $scope.form = {
      id: '',
      serial_no: '',
      sticker: '',
      comment: ''
    }
  }

  $scope.createPersonassetModal = function () {
    clearForm();
  }

  $scope.createPersonasset = function () {
    $http.post('/api/personasset/create', $scope.form).success(function (data) {
      getPage(1);
      clearForm();
    }).error(function (data, status) {
      $scope.formErrors = data;
    });
  }

  $scope.removeEntry = function (id) {
    var isConfirmDelete = confirm('Are you sure to DELETE this customer asset?');
    if (isConfirmDelete) {
      $http.delete('/api/transactionpersonasset/' + id + '/delete').success(function (data) {
        getPage(1);
      });
    } else {
      return false;
    }
  }

  $scope.editPersonassetMovementModal = function (personassetmovement) {
    fetchSinglePersonassetMovement(personassetmovement);
  }

  function fetchSinglePersonassetMovement(data) {
    $scope.form = {
      id: data.id,
      serial_no: data.serial_no,
      sticker: data.sticker,
      remarks: data.remarks
    }
  }

  function fetchPeopleApi() {
    $http.get('/api/people/options').success(function (data) {
      $scope.people = data;
    });
  }

  $scope.updatePersonassetMovement = function () {
    $http.post('/api/transactionpersonasset/update', $scope.form).success(function (data) {
      getPage(1);
    });
  }

  $scope.isFormValid = function () {
    if ($scope.form.serial_no && $scope.form.sticker && $scope.form.remarks) {
      return false;
    } else {
      return true;
    }
  }
}

app.controller('personassetCategoryController', personassetCategoryController);
app.controller('personassetMovementController', personassetMovementController);
app.controller('personassetCurrentController', personassetCurrentController);