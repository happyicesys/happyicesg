// rpt_index.js
var app = angular.module('app', [
                                    // 'ui.bootstrap',
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    'ui.bootstrap.datetimepicker',
                                    '720kb.datepicker'
                                ]);

    function rptController($scope, $http){
        $scope.currentPage = 1;
        $scope.matchTotal = false;
        $scope.indexData = {
            delivery_date: moment().format("YYYY-MM-DD"),
            paid_at: moment().format("YYYY-MM-DD"),
        };
        $scope.transaction = {
            payMethodModel: 'cash',
        }
        $scope.search = {
            id: '',
            cust_id: '',
            company: '',
            status: '',
            pay_status: '',
            profile_id: ''
        }

        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "DailyRpt"+ now + ".xls");
        };

        $scope.today = moment().format("YYYY-MM-DD");

        angular.element(document).ready(function () {
            $('.select_profile').select2();

            $http.get('/user/data/' + $('#user_id').val()).success(function(person){
                var driver = false;
                for(var i = 0; i < person.roles.length; i++){
                    if(person.roles[i].name === 'driver'){
                        driver = true;
                        break;
                    }
                }
                $scope.getdriver = function(){
                    return driver;
                }
            });

            // first init
            getIndex();

            function getIndex(){
                $http.post('/report/dailyrpt', $scope.indexData).success(function(transactions){
                    $scope.transactions = transactions;
                    $scope.All = transactions.length;
                });

                $http.post('/report/dailyrec', $scope.indexData).success(function(rptdata){
                    $scope.rptdata = rptdata;
                    if(rptdata.amt_mod == rptdata.cash_mod + rptdata.cheque_mod + rptdata.tt_mod) {
                        $scope.matchTotal = true;
                    }else {
                        $scope.matchTotal = false;
                    }
                });
            }

            $scope.syncData = function(){
                $scope.indexData = {
                    delivery_date: $scope.delivery_date,
                    paid_at: $scope.paid_at,
                    paid_by: $scope.paid_by,
                    driver: $scope.driver,
                    role: $scope.role,
                    transaction_id: $scope.search.id,
                    cust_id: $scope.search.cust_id,
                    company: $scope.search.company,
                    status: $scope.search.status,
                    pay_status: $scope.search.pay_status,
                    profile_id: $scope.search.profile_id
                }
            }

            $scope.dateChange = function(date){
                if(date){
                    $scope.delivery_date = moment(date).format("YYYY-MM-DD");
                    $scope.paid_at = moment(date).format("YYYY-MM-DD");
                }else{
                    $scope.delivery_date = '';
                    $scope.paid_at = '';
                }
                $scope.syncData();
                getIndex();
            }

            $scope.dateChange2 = function(date){
                if(date){
                    $scope.paid_at = moment(date).format("YYYY-MM-DD");
                    $scope.delivery_date = moment(date).format("YYYY-MM-DD");
                }else{
                    $scope.delivery_date = '';
                    $scope.paid_at = '';
                }
                $scope.syncData();
                getIndex();
            }


            $scope.onPrevSingleClicked = function(date) {
                var edited = date ? moment(new Date(date)).subtract(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
                $scope.dateChange2(edited);
            }

            $scope.onNextSingleClicked = function(date) {
                var edited = date ? moment(new Date(date)).add(1, 'days').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
                $scope.dateChange2(edited);
            }

            $scope.dbSearch = function(){
                $scope.syncData();
                getIndex();
            }

            $scope.paidByChange = function(paid_by){
                $scope.driver = paid_by;
                $scope.syncData();
                getIndex();
            }


            $scope.driverChange = function(driver){
                $scope.paid_by = driver;
                $scope.syncData();
                getIndex();
            }

            $scope.onRoleChanged = function(role){
                $scope.role = role;
                $scope.syncData();
                getIndex();
            }
        });

        $scope.onVerifiedPaid = function($event, transaction_id, payMethodModel, noteModel){
            $event.preventDefault();

            $http({
                url: '/transaction/rpt/' + transaction_id ,
                method: "POST",
                data:{
                        paymethod: payMethodModel,
                        note: noteModel,
                    },
                }).success(function(response){
                    $http.get('/transaction/status/'+ transaction_id).success(function(){
                        location.reload();
                    });
                });
        }

        $scope.exportAction = function(){
            switch($scope.export_action){
                case 'pdf': $scope.$broadcast('export-pdf', {});
                          break;
                case 'excel': $scope.$broadcast('export-excel', {});
                          break;
                case 'doc': $scope.$broadcast('export-doc', {});
                          break;
                default: console.log('no event caught');
            }
        }
    }

app.filter('delDate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

app.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

(function(){
//export html table to pdf, excel and doc format directive
var exportTable = function(){

    var link = function($scope, elm, attr){
        $scope.$on('export-pdf', function(e, d){
            elm.tableExport({type:'pdf', escape:false});
        });
        $scope.$on('export-excel', function(e, d){
            elm.tableExport({type:'excel', escape:false});
        });
        $scope.$on('export-doc', function(e, d){
            elm.tableExport({type: 'doc', escape:false});
        });
    }

return {
    restrict: 'C',
    link: link
   }
}

angular
    .module('CustomDirectives', [])
    .directive('exportTable', exportTable);
})();


app.controller('rptController', rptController);
app.controller('repeatController', repeatController);
