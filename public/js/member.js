var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function memberController($scope, $http){

        $scope.currentPage = 1;
        $scope.itemsPerPage = 50;
        var user_id = $('#user_id').val();

        angular.element(document).ready(function () {

            $http.get('/person/user/' + user_id). success(function(person){

                $scope.person = person;

            });

            $http.get('/market/member/data').success(function(members){
                $scope.members = members;
                $scope.All = members.length;
            });

            $scope.exportData = function () {
                var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                    type: "application/vnd.ms-excel;charset=charset=utf-8"
                });
                var now = Date.now();
                saveAs(blob, "CustomerRpt"+ now + ".xls");
            };
        });
    }

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

$(function() {
    // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // save the latest tab; use cookies if you like 'em better:
        localStorage.setItem('lastTab', $(this).attr('href'));
    });

    // go to the latest tab, if it exists:
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('[href="' + lastTab + '"]').tab('show');
    }
});

app.controller('memberController', memberController);
app.controller('repeatController', repeatController);
