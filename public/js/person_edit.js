var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'ui.select', 'ngSanitize']);

    function personEditController($scope, $http){
        $scope.currentPage = 1;
        $scope.itemsPerPage = 10;  

        angular.element(document).ready(function () {

            $http.get('/person/transac/'+ $('#person_id').val()).success(function(transactions){
                $scope.transactions = transactions; 
                $scope.All = transactions.length;      
            });            

            //delete record
            $scope.confirmDelete = function(id){
                var isConfirmDelete = confirm('Are you sure you want to delete the entry');
                if(isConfirmDelete){
                    $http({
                        method: 'DELETE',
                        url: '/transaction/data/' + id
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
                saveAs(blob, "TransactionRpt"+ now + ".xls");
            };

            $http.get('/item/data').success(function(items){
                $scope.items = items;
            });  

        });

        $http.get('/person/price/'+ $('#person_id').val()).success(function(prices){
            $scope.prices = prices; 
            $scope.getRetailInit = function(item_id){
                var retailNum = 0;
                for(var i = 0; i < $scope.prices.length; i ++){
                    var price = $scope.prices[i];
                    if(item_id == price.item_id){
                        retailNum = price.retail_price;
                        return retailNum;     
                    }
                }
            } 

            $scope.getQuoteInit = function(item_id){
                var quoteNum = 0;
                for(var i = 0; i < $scope.prices.length; i ++){
                    var price = $scope.prices[i];
                    if(item_id == price.item_id){
                        quoteNum = price.quote_price;
                        return quoteNum;     
                    }
                }
            }                 
        });

        $http.get('/person/specific/data/'+ $('#person_id').val()).success(function(person){
            $scope.personData = person;
            $scope.noteModel = person.note;

            $scope.getRetailChange = function(retailModel){
                $scope.afterChange = (retailModel * person.cost_rate/100).toFixed(2);
            }
/*
            $scope.noteSave = function(note){
                console.log(note);
                $http({
                    method: 'POST',
                    url: '/person/' + person.id + '/note',
                    data: $.param(note: 'note'),
                }).success(function(){
                    });
               
            }   */
/*            $scope.noteSave = function(note){
                $http.post({'/note', note})
                        .success(function(){
                        });
            }*/

        });            
   
    }  

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}    

app.controller('personEditController', personEditController);
app.controller('repeatController', repeatController);
