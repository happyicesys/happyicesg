var app = angular.module('app', [
    'ngSanitize',
    'ui.select2',
    'ui.select',
    'angularUtils.directives.dirPagination',
    '720kb.datepicker',
], ['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.post['X-CSRF-TOKEN'] = $('meta[name=csrf-token]').attr('content');
}]);


function jobController($scope, $http, $window) {
    // init the variables
    $scope.alldata = [];
    $scope.totalCount = 0;
    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 100;
    $scope.indexFrom = 0;
    $scope.indexTo = 0;
    $scope.today = moment().format("YYYY-MM-DD");
    $scope.tomorrow = moment().add(1, 'days').format('YYYY-MM-DD');
    $scope.search = {
        task_name: '',
        from: $scope.today,
        to: $scope.tomorrow,
        progress: '',
        workers: '',
        pageNum: 100,
        sortName: '',
        sortBy: true
    }
    $scope.form = {
        task_name: '',
        task_date: '',
        remarks: '',
        progress: '',
        workers: ''
    }
    // init page load
    getPage(1, true);

    angular.element(document).ready(function () {
        $('.select').select2();
        $('.selectmultiple').select2({
            placeholder: 'Choose one or many..'
        });
    });

    $scope.exportData = function () {
        var blob = new Blob(["\ufeff", document.getElementById('exportable_job').innerHTML], {
            type: "application/vnd.ms-excel;charset=charset=utf-8"
        });
        var now = Date.now();
        saveAs(blob, "Job Card" + now + ".xls");
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

    $scope.fromChange = function (date) {
        if (date) {
            $scope.search.from = moment(new Date(date)).format('YYYY-MM-DD');
        }
        $scope.searchDB();
    }

    $scope.toChange = function (date) {
        if (date) {
            $scope.search.to = moment(new Date(date)).format('YYYY-MM-DD');
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
        $http.post('/api/jobs?page=' + pageNumber + '&init=' + first, $scope.search).success(function (data) {
            if (data.jobs.data) {
                $scope.alldata = data.jobs.data;
                $scope.totalCount = data.jobs.total;
                $scope.currentPage = data.jobs.current_page;
                $scope.indexFrom = data.jobs.from;
                $scope.indexTo = data.jobs.to;
            } else {
                $scope.alldata = data.jobs;
                $scope.totalCount = data.jobs.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.jobs.length;
            }
            $scope.All = data.jobs.length;
            $scope.spinner = false;
        });
    }

    $scope.createJobModal = function () {
        $scope.form = {
            id: '',
            task_name: '',
            task_date: $scope.today,
            remarks: '',
            progress: '',
            workers: ''
        }
    }

    $scope.createJob = function () {
        $http.post('/api/job/create', $scope.form).success(function (data) {
            getPage(1);

            $scope.form = {
                id: '',
                task_name: '',
                task_date: '',
                remarks: '',
                progress: '',
                workers: ''
            }
        }).error(function (data, status) {
            $scope.formErrors = data;
        });
    }

    $scope.removeEntry = function (id) {
        var isConfirmDelete = confirm('Are you sure to DELETE this job card?');
        if (isConfirmDelete) {
            $http.delete('/api/job/' + id + '/delete').success(function (data) {
                getPage(1);
            });
        } else {
            return false;
        }
    }

    $scope.editJobModal = function (job) {
        fetchSingleJob(job);
    }

    function fetchSingleJob(job) {
        $scope.form = {
            id: job.id,
            task_name: job.task_name,
            task_date: job.task_date,
            remarks: job.remarks,
            progress: job.progress,
            workers: ''
        }
    }

    $scope.editJob = function () {
        $http.post('/api/job/update', $scope.form).success(function (data) {
            getPage(1);
        });
    }

    $scope.isFormValid = function () {
        if ($scope.form.task_name) {
            return false;
        } else {
            return true;
        }
    }

    $scope.taskDateChanged = function (date) {
        if (date) {
            $scope.form.task_date = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }

    $scope.verifyJob = function (event, job, is_verify) {
        event.preventDefault();
        $http.post('/api/job/verify', {
            job_id: job.id,
            is_verify: is_verify
        }).success(function (data) {
            getPage(1, false);
        });
    }    

}

app.controller('jobController', jobController);