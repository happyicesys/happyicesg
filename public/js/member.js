var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination']);

    function memberController($scope, $http){

        $scope.currentPage = 1;
        $scope.itemsPerPage = 50;


        angular.element(document).ready(function () {

            $http.get('/market/member/data').success(function(members){
                $scope.members = members;
                $scope.All = members.length;
            });

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete the entry');
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/member/data/' + id
                    })
                    .success(function(data){
                        location.reload();
                    })
                    .error(function(data){
                        alert('Unable to delete');
                    })
                }else{
                    return false;
                }
            }

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
