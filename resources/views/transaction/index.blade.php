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
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
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
                <div style="padding-bottom: 10px">
                    <label for="search_name" class="search">Search Invoice:</label>
                    <input type="text" ng-model="search.id">
                    <label for="search_company" class="search" style="padding-left: 10px">Company:</label>
                    <input type="text" ng-model="search.person.company">
                    <label for="search_contact" class="search" style="padding-left: 10px">Status:</label>
                    <input type="text" ng-model="search.status">
                </div>
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>                    
                        <th class="col-md-1 text-center">
                            <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                            Invoice No
                            <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>                                                       
                        </th>
                        <th class="col-md-2 text-center">
                            Company   
                        </th>
                         <th class="col-md-1 text-center">
                            Payment
                        </th>                         
                        <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                            Delivery Date
                            <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="#" ng-click="sortType = 'status'; sortReverse = !sortReverse">
                            Status
                            <span ng-show="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span>                            
                        </th>                                                
                         <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'created_at'; sortReverse = !sortReverse">                         
                            Created On
                            <span ng-show="sortType == 'created_at' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'created_at' && sortReverse" class="fa fa-caret-up"></span>                          
                        </th>
                         <th class="col-md-1 text-center">
                            Created By
                        </th>                        
                         <th class="col-md-2 text-center">
                            Action
                        </th>                                                                                                
                    </tr>

                    <tbody>
                        <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                            <td class="col-md-1 text-center">@{{ number }} </td>
                            <td class="col-md-1 text-center">@{{ transaction.id }} </td>
                            <td class="col-md-2 text-center">
                            <a href="/person/@{{ transaction.person.id }}">
                            @{{ transaction.person.company }}
                            </a>
                            </td>
                            <td class="col-md-1 text-center">@{{ transaction.pay_status }}</td>
                            <td class="col-md-2 text-center">@{{ transaction.delivery_date }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.status }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.created_at }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.user.name }}</td>
                            <td class="col-md-2 text-center">
                                    <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(transaction.id)">Delete</button>  
                            </td>
                        </tr>
                        <tr ng-show="(transactions | filter:search).length == 0 || ! transactions.length">
                            <td colspan="8" class="text-center">No Records Found</td>
                        </tr>                         

                    </tbody>
                </table>            
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(transactions | filter:search).length}} of @{{transactions.length}} entries</label> 
                </div>
        </div>
    </div>  

    <script src="/js/transaction_index.js"></script>  
@stop