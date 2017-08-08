var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker'
                                ]);

function freezeDateController($scope, $http){
    $scope.form = {};

    angular.element(document).ready(function () {
        $http.get('/api/transaction/freeze/date').success(function(data){
            $scope.form.freeze_date = data.INVOICE_FREEZE_DATE ? moment(new Date(data.INVOICE_FREEZE_DATE)).format('YYYY-MM-DD') : '';
        });
    });

    $scope.freezeDateChanged = function(date){
        if(date){
            $scope.form.freeze_date = moment(new Date(date)).format('YYYY-MM-DD');
        }
    }
}

app.controller('freezeDateController', freezeDateController);

