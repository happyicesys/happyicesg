var app = angular.module('app', [
  'angularUtils.directives.dirPagination',
  'ui.select',
  'ngSanitize',
  '720kb.datepicker'
]);

function routeTemplateController($scope, $http){
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
        cust_id: '',
        strictCustId: '',
        custcategory: '',
        company: '',
        zone_id: '',
        pageNum: 'All',
        profile_id: '',
        invoice_date: ''
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
        saveAs(blob, "Route Template"+ now + ".xls");
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

    // on generate button clicked
    $scope.onGenerateClicked = function() {
      let isConfirmGenerate = confirm('Are you sure you want to generate the invoices?');
      if(isConfirmGenerate){
        $http.post('/api/route-template/generate', {invoiceDate: $scope.search.invoice_date, driver: $scope.search.driver, alldata: $scope.alldata}).success(function(data) {
          getPage(1)
          $scope.checkall = false;
        }).error(function(data) {
          alert('Unable to Generate');
        })
      }else{
          return false;
      }

    }

    // checkbox all
    $scope.onCheckAllChecked = function() {
        var checked = $scope.checkall;

        $scope.alldata.forEach(function (transaction, key) {
            $scope.alldata[key].check = checked;
        });
    }

    $scope.merchandiserInit = function(userId) {
        $scope.search.account_manager = userId;
    }

    // on add route button clicked
    $scope.onAddRouteClicked = function() {
      const person = JSON.parse($scope.form.person);
      const sequence = $scope.form.sequence;
      $scope.form.route_template_items.push({
        person: person,
        sequence: sequence
      });
      $scope.form.sequence = ''
    }

    // delete single entry api
    $scope.onSingleEntryDeleted = function(item) {
      let index = $scope.form.route_template_items.indexOf(item);
      $scope.form.route_template_items.splice(index, 1)
    }

    // upon form submit
    $scope.onFormSubmitClicked = function() {
      $http.post('/api/route-template/store-update', $scope.form).success(function(data) {
        $scope.form = getDefaultForm()
        $('.select').select2({
          placeholder: 'Select...'
        });
        getPage(1)
      });
    }

    $scope.onAddRouteTemplateButtonClicked = function() {
      $scope.form = getDefaultForm()
      $('.select').select2({
        placeholder: 'Select...'
      });
    }

    // single edit entry clicked
    $scope.onSingleRouteTemplateClicked = function(routeTemplate) {
      $scope.form = getDefaultForm()
      $('.select').select2({
        placeholder: 'Select...'
      });
      console.log(routeTemplate);
      $scope.form = routeTemplate
    }

    // on route template removed
    $scope.onSingleEntryRemoved = function(id) {
      let isConfirmRemove = confirm('Are you sure you want to remove the template?');

      if(isConfirmRemove) {
        $http.delete('/api/route-template/delete/' + id).success(function(data) {
          getPage(1);
        })
      }else {
        return false;
      }
    }

    function getDefaultForm() {
      return {
        id: '',
        name: '',
        desc: '',
        person: '',
        sequence: '',
        route_template_items: []
      }
    }

    // retrieve page w/wo search
    function getPage(pageNumber, first){
        $scope.spinner = true;
        $http.post('/api/route-template?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
            if(data.routeTemplates.data){
                $scope.alldata = data.routeTemplates.data;
                $scope.totalCount = data.routeTemplates.total;
                $scope.currentPage = data.routeTemplates.current_page;
                $scope.indexFrom = data.routeTemplates.from;
                $scope.indexTo = data.routeTemplates.to;
            }else{
                $scope.alldata = data.routeTemplates;
                $scope.totalCount = data.routeTemplates.length;
                $scope.currentPage = 1;
                $scope.indexFrom = 1;
                $scope.indexTo = data.routeTemplates.length;
            }
            // get total count
            $scope.All = data.routeTemplates.length;

            // return total amount
            $scope.spinner = false;
        });
    }
}

app.controller('routeTemplateController', routeTemplateController);
