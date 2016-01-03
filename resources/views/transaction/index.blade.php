@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/transaction"><h1>{{ $TRANS_TITLE }} <i class="fa fa-briefcase"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="transController">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_num">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='30'">
                          <option>10</option>
                          <option>20</option>
                          <option>30</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        <a href="/transaction/create" class="btn btn-success">+ New {{ $TRANS_TITLE }}</a>                        
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="search_name" class="search">Search Inv:</label>
                        <input type="text" ng-model="search.id">
                        <label for="search_company" class="search" style="padding-left: 10px">ID:</label>
                        <input type="text" ng-model="search.person.cust_id">
                        <label for="search_status" class="search" style="padding-left: 10px">Status:</label>
                        <input type="text" ng-model="search.status">
                        <label for="search_payment" class="search" style="padding-left: 10px">Payment:</label>
                        <input type="text" ng-model="search.pay_status">                    
                    </div>
                </div>
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="del_from" class="search">Delivery On:</label>
                        <input type="text" ng-model="search.delivery_date">
                        <label for="search_status" class="search" style="padding-left: 10px">Created On:</label>
                        <input type="text" ng-model="search.created_at"> 
                        <label for="search_updated_by" class="search" style="padding-left: 10px">Last Modified:</label>
                        <input type="text" ng-model="search.updated_by">                                                                  
                    </div>
                </div>
                <div class="row">
                    <div style="padding:0px 0px 10px 5px">
                        <label for="search_driver" class="search" style="padding-left: 10px">Delivered By:</label>
                        <input type="text" ng-model="search.driver">                         
                    </div>
                </div>
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>                
                    </div>
                </div>                
                <div id="exportable" class="table-responsive">
                    <div class="table-responsive">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>                    
                                <th class="col-md-1 text-center">
                                    <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                                    INV #
                                    <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>                                                       
                                </th>
                                <th class="col-md-1 text-center">
                                    ID   
                                </th>                                                                                  
                                <th class="col-md-1 text-center">
                                    Company   
                                </th>
                                <th class="col-md-1 text-center">
                                    Del Postcode   
                                </th>                             
                                 <th class="col-md-1 text-center">
                                    Payment
                                </th>                         
                                <th class="col-md-1 text-center">
                                    <a href="#" ng-click="sortType = 'delivery_from'; sortReverse = !sortReverse">
                                    Delivery Date
                                    <span ng-show="sortType == 'delivery_from' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'delivery_from' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="#" ng-click="sortType = 'status'; sortReverse = !sortReverse">
                                    Status
                                    <span ng-show="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span>                            
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="#" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                                    Total Amount
                                    <span ng-show="sortType == 'total' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'total' && sortReverse" class="fa fa-caret-up"></span>                            
                                </th>                                                                             
                                <th class="col-md-1 text-center">
                                    <a href="#" ng-click="sortType = 'created_at'; sortReverse = !sortReverse">                         
                                    Created On
                                    <span ng-show="sortType == 'created_at' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'created_at' && sortReverse" class="fa fa-caret-up"></span>                          
                                </th>
                                <th class="col-md-1 text-center">
                                    Last Modified
                                </th> 
                                <th class="col-md-1 text-center">
                                    Delivered By
                                </th>                                                                                 
                                <th class="col-md-1 text-center">
                                    Action
                                </th>                                                                                                
                            </tr>

                            <tbody>
                                <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                    <td class="col-md-1 text-center">@{{ number }} </td>
                                    <td class="col-md-1 text-center">@{{ transaction.id }} </td>
                                    <td class="col-md-1 text-center">@{{ transaction.person.cust_id }} </td>                                
                                    <td class="col-md-1 text-center">
                                    <a href="/person/@{{ transaction.person.id }}">
                                    @{{ transaction.person.company }}
                                    </a>
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.person.del_postcode }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.pay_status }}</td>
                                    <td class="col-md-1 text-center">
                                        @{{ transaction.delivery_date }}
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.status }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.total }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.created_at }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.driver}}</td>
                                    <td class="col-md-1 text-center">        
                                            <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending'">Print</a>
                                            <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-warning">Edit</a> 
                                    </td>
                                </tr>
                                <tr ng-if="(transactions | filter:search).length == 0 || ! transactions.length">
                                    <td colspan="14" class="text-center">No Records Found</td>
                                </tr>                         

                            </tbody>
                        </table>
                    </div>
                </div>            
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(transactions | filter:search).length}} of @{{transactions.length}} entries</label> 
                </div>
        </div>
    </div>  
    <script src="/js/transaction_index.js"></script>  
@stop