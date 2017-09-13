var app = angular.module('app', [
                                    'angularUtils.directives.dirPagination',
                                    'ui.select',
                                    'ngSanitize',
                                    '720kb.datepicker',
                                    'datePicker'
                                ]);

    function personEditController($scope, $http){
        // init the variables
        $scope.alldata = [];
        $scope.datasetTemp = {};
        $scope.totalCountTemp = {};
        $scope.totalCount = 0;
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.itemsPerPage = 100;
        $scope.indexFrom = 0;
        $scope.indexTo = 0;
        $scope.sortBy = true;
        $scope.sortName = '';
        $scope.headerTemp = '';
        $scope.today = moment().format("YYYY-MM-DD");
        $scope.search = {
            id: '',
            status: '',
            pay_status: '',
            delivery_from: '',
            delivery_to: '',
            driver: '',
            pageNum: 100,
        }
        $scope.total_amount = 0.00;
        $scope.total_paid = 0.00;
        $scope.total_owe = 0.00;
        // init page load
        getPage(1, true);

        loadFiles();

        angular.element(document).ready(function () {
            $('.select').select2();
        });
        $scope.onDeliveryFromChanged = function(date){
            if(date){
                $scope.search.delivery_from = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.onDeliveryToChanged = function(date){
            if(date){
                $scope.search.delivery_to = moment(new Date(date)).format('YYYY-MM-DD');
            }
            $scope.searchDB();
        }
        $scope.exportData = function () {
            var blob = new Blob(["\ufeff", document.getElementById('exportable').innerHTML], {
                type: "application/vnd.ms-excel;charset=charset=utf-8"
            });
            var now = Date.now();
            saveAs(blob, "TransactionRpt"+ now + ".xls");
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

          // when hitting search button
        $scope.searchDB = function(){
            $scope.sortName = '';
            $scope.sortBy = '';
            getPage(1, false);
        }

        // retrieve page w/wo search
        function getPage(pageNumber, first){
            $scope.spinner = true;
            $http.post('/person/transac/' + $('#person_id').val() +'?page=' + pageNumber + '&init=' + first, $scope.search).success(function(data){
                if(data.transactions.data){
                    $scope.alldata = data.transactions.data;
                    $scope.totalCount = data.transactions.total;
                    $scope.currentPage = data.transactions.current_page;
                    $scope.indexFrom = data.transactions.from;
                    $scope.indexTo = data.transactions.to;
                }else{
                    $scope.alldata = data.transactions;
                    $scope.totalCount = data.transactions.length;
                    $scope.currentPage = 1;
                    $scope.indexFrom = 1;
                    $scope.indexTo = data.transactions.length;
                }
                // get total count
                $scope.All = data.transactions.length;

                // return total amount
                $scope.total_amount = data.total_amount;
                $scope.total_paid = data.total_paid;
                $scope.total_owe = data.total_owe;
                $scope.profileDealsGrossProfit = data.profileDealsGrossProfit;
                $scope.spinner = false;
            });
        }

/*
            $http.get('/person/transac/'+ $('#person_id').val()).success(function(transactions){
                $scope.transactions = transactions;
                $scope.All = transactions.length;
            });*/


        $http.get('/item/data').success(function(data){
            $scope.items = data.items;
        });

        // $scope.loadFiles();

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

        // loading files from person
        function loadFiles() {
            $http.get('/api/person/files/' + $('#person_id').val()).success(function(data) {
                $scope.files = data;
            });
        }

          // removing file
        $scope.removeFile = function(file_id){
            $http.post('/api/person/file/remove', {'file_id': file_id}).success(function(data) {
                loadFiles();
            });
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
  return function(input, scope) {
    if (input!=null) {
        input = input.toLowerCase();
        return input.substring(0,1).toUpperCase()+input.substring(1);
    }else {
        return null;
    }
  }
});

app.directive('ngConfirmClick', [
    function(){
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmClick || "Are you sure?";
                var clickAction = attr.confirmedClick;
                element.bind('click',function (event) {
                    if ( window.confirm(msg) ) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
}])

function repeatController($scope) {
    $scope.$watch('$index', function(index) {
        $scope.number = ($scope.$index + 1) + ($scope.currentPage - 1) * $scope.itemsPerPage;
    })
}

app.controller('personEditController', personEditController);
app.controller('repeatController', repeatController);
