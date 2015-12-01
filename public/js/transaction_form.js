var app = angular.module('app', [   'ui.bootstrap', 
                                    'angularUtils.directives.dirPagination',
                                    'ui.select', 
                                    'ngSanitize'
                                ]);

    function transactionController($scope, $http){

    $scope.currentPage = 1;
    $scope.itemsPerPage = 10;  

        $http.get('/transaction/customer').success(function(people){
            $scope.person = {};
            $scope.people = people;


            $scope.onPersonSelected = function (person){
                
            $scope.billModel = person.bill_to;
            $scope.delModel = person.del_address;
            $scope.paytermModel = person.payterm;
            $('.date').datetimepicker({
            format: 'DD-MMMM-YYYY'
            });
            $('.date').val('');

            $http({
                url: '/transaction/item/'+ person.id,
                method: "GET",
            
            }).success(function(items){ 
                $scope.item = {};
                $scope.items = items;
                $scope.qtyModel = [];
                $scope.amountModel = [];
                $scope.unitModel = [];

                $scope.onItemSelected = function (item_id){

                    $http({
                        url: '/transaction/person/'+ person.id + '/item/' + item_id,
                        method: "GET",

                    }).success(function(prices){
                        $scope.prices = prices;
                        $scope.qtyModel = 1;
                        $scope.unitModel = prices.item.unit;
                        $scope.amountModel = prices.quote_price;

                        $scope.onQtyChange = function(){
                            $scope.amountModel = prices.quote_price * $scope.qtyModel;
                        }
                    });                    

                }

            });

            }
        });

/*        $http({
            url: '/deal/data' + 
        });*/
        /*$http.get('/deal/data').success(function(deals){
            $scope.deals = deals;
        });*/

            //save new record / update existing record
        $scope.save = function(modalstate, id) {
            var url = API_URL + "employees";
            
            //append employee id to the URL if the form is in edit mode
            if (modalstate === 'edit'){
                url += "/" + id;
            }
            
            $http({
                method: 'POST',
                url: url,
                data: $.param($scope.employee),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(response) {
                console.log(response);
                location.reload();
            }).error(function(response) {
                console.log(response);
                alert('This is embarassing. An error has occured. Please check the log for details');
            });
        }


        //delete record
        $scope.confirmDelete = function(id){
            var isConfirmDelete = confirm('Are you sure you want to delete entry ID: ' + id);
            if(isConfirmDelete){
                $http({
                    method: 'DELETE',
                    url: '/person/data/' + id
                })
                .success(function(data){
                    console.log(data);
                    location.reload();
                })
                .error(function(data){
                    console.log(data);
                    alert('Unable to delete');
                })
            }else{
                return false;
            }
        } 
    }  

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    

app.controller('transactionController', transactionController);
app.controller('repeatController', repeatController);
