var app = angular.module('app', [
    'ngSanitize',
    'ui.select2',
    'ui.select',
    'angularUtils.directives.dirPagination',
    '720kb.datepicker',
], ['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.post['X-CSRF-TOKEN'] = $('meta[name=csrf-token]').attr('content');
}]);


function personmaintenanceController($scope, $http, $window) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.weekstart = moment().startOf('isoWeek').format("YYYY-MM-DD");
    $scope.weekend = moment().endOf('isoWeek').format("YYYY-MM-DD");
    $scope.search = {
        title: '',
        person_id: '',
        created_from: $scope.weekstart,
        created_to: $scope.weekend,
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        title: '',
        person_id: '',
        remarks: '',
        is_refund: '',
        refund_name: '',
        refund_bank: '',
        refund_account: '',
        refund_contact: '',
    }
    // init page load
    getPage(1, true);
    fetchPeopleApi();

    angular.element(document).ready(function () {
        $('.select').select2();
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_personmaintenance').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Maintenance Log" + now + ".xls");
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

    // retrieve page w/wo search
    function getPage(pageNumber, first) {
        $scope.spinner = true;
        $http.post('/api/personmaintenances?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.personmaintenances.data) {
                $scope.alldata = data.personmaintenances.data;
                $scope.totalCount = data.personmaintenances.total;
                $scope.currentPage = data.personmaintenances.current_page;
                $scope.indexFrom = data.personmaintenances.from;
                $scope.indexTo = data.personmaintenances.to;
            } else {
                $scope.alldata = data.personmaintenances;
                $scope.totalCount = data.personmaintenances.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.personmaintenances.length;
            }
            $scope.All = data.personmaintenances.length;
            $scope.spinner = false;
        });
    }

    $scope.createPersonmaintenanceModal = function () {
        $scope.form = {
            title: '',
            person_id: '',
            remarks: '',
            is_refund: '',
            refund_name: '',
            refund_bank: '',
            refund_account: '',
            refund_contact: '',
            created_at: moment().format('YYYY-MM-DD') 
        }
    }

    $scope.createPersonmaintenance = function () {
        $http.post('/api/personmaintenance/create', $scope.form).success(function (data) {
            getPage(1);

            $scope.form = {
                title: '',
                person_id: '',
                remarks: '',
                is_refund: '',
                refund_name: '',
                refund_bank: '',
                refund_account: '',
                refund_contact: '', 
                created_at: moment().format('YYYY-MM-DD')               
            }
        }).error(function (data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function (id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this maintenance log?');
        if (isConfirmDelete) {
            $http.delete('/api/personmaintenance/' + id + '/delete').success(function (data) {
                getPage(1);
            });
        } else {
            return false;
        }
    }

    $scope.editPersonmaintenanceModal = function (personmaintenance) {
        fetchSinglePersonmaintenance(personmaintenance);
    }

    function fetchSinglePersonmaintenance(personmaintenance) {
        $scope.form = {
            id: personmaintenance.id,
            title: personmaintenance.title,
            person_id: personmaintenance.person_id,
            remarks: personmaintenance.remarks,
            is_refund: personmaintenance.is_refund,
            refund_name: personmaintenance.refund_name,
            refund_bank: personmaintenance.refund_bank,
            refund_account: personmaintenance.refund_account,
            refund_contact: personmaintenance.refund_contact,  
            created_at: personmaintenance.created_at          
        }
    }

    function fetchPeopleApi() {
        $http.get('/api/people/options').success(function(data) {
            $scope.people = data;
        });
    }

    $scope.editPersonmaintenance = function () {
        $http.post('/api/personmaintenance/update', $scope.form).success(function (data) {
            getPage(1);
        });
    }

    $scope.isFormValid = function() {
        if($scope.form.title || $scope.form.person_id || $scope.form.remarks) {
            return false;
        }else {
            return true;
        }
    }

    $scope.createdAtChanged = function(date) {
        if (date) {
            $scope.form.created_at = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

}

app.controller('personmaintenanceController', personmaintenanceController);