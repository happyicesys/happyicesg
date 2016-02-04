@inject('profiles', 'App\Profile')

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
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='70'">
                          <option ng-value="10">10</option>
                          <option ng-value="30">30</option>
                          <option ng-value="70">70</option>
                          <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>
                    <div class="col-md-6 col-md-offset-2" style="padding-left:200px">
                        <div class="col-md-3"  style="padding-top:10px">
                            <label for="profile_id" class="search">Profile:</label>
                        </div>
                        <div class="col-md-9" style="padding-top:10px">
                            {!! Form::select('profile_id', [''=>'All']+$profiles::lists('name', 'name')->all(), null, ['id'=>'profile_id', 
                                'class'=>'select', 
                                'ng-model'=>'search.person.profile.name']) 
                            !!}
                        </div>
                    </div>   
                    <div class="pull-right">                    
                        <a href="/transaction/create" class="btn btn-success">+ New {{ $TRANS_TITLE }}</a>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="search_inv" class="search">Search Inv:</label>
                        <input type="text" ng-model="search.id" style="width:140px;">
                        <label for="search_id" class="search" style="padding-left: 10px">ID:</label>
                        <input type="text" ng-model="search.person.cust_id" style="width:140px;">
                        <label for="search_company" class="search" style="padding-left: 10px">Company:</label>
                        <input type="text" ng-model="search.person.company" style="width:140px;">
                        <label for="search_status" class="search" style="padding-left: 10px">Status:</label>
                        <input type="text" ng-model="search.status" style="width:140px;">                        
                    </div>
                </div>
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="search_payment" class="search">Payment:</label>
                        <input type="text" ng-model="search.pay_status" style="width:140px;"> 
                        <label for="search_updated_by" class="search" style="padding-left: 10px">Last Modified By:</label>
                        <input type="text" ng-model="search.updated_by" style="width:140px;">
                        <label for="search_updated_by" class="search" style="padding-left: 10px">Last Modified Date:</label>
                        <input type="text" ng-model="search.updated_at" style="width:140px;">                      
                    </div>
                </div>
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="del_on" class="search pull-left">Delivery On:</label>
                        <div class="col-md-2" style="padding-top:3px;">
                            <div class="dropdown" style="width:140px;">
                                <a class="dropdown-toggle" id="dropdown2" role="button" data-toggle="dropdown" data-target="#" href="#">
                                <div class="input-group"><input type="text" style="width:140px;" data-ng-model="search.delivery_date" ng-init="search.delivery_date=today">
                                </div>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                <datetimepicker data-ng-model="search.delivery_date" data-datetimepicker-config="{ dropdownSelector: '#dropdown2', minView: 'day'}" ng-change="dateChange(search.delivery_date)"/>
                                </ul>
                            </div>
                        </div>

                        <label for="search_driver" class="search" style="margin-left:-20px;">Delivered By:</label>
                        <input type="text" ng-model="search.driver" style="width:140px;">
                    </div>                   
                </div>              
                
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                        <label class="pull-right" style="padding-right:18px;" for="totalnum">Showing @{{(transactions | filter:search).length}} of @{{transactions.length}} entries</label>
                    </div>
                </div>                
                    <div class="table-responsive" id="exportable">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>                    
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                                    INV #
                                    <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'person.cust_id'; sortReverse = !sortReverse">
                                    ID
                                    <span ng-show="sortType == 'person.cust_id' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'person.cust_id' && sortReverse" class="fa fa-caret-up"></span>
                                </th>                                                                                  
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'person.company'; sortReverse = !sortReverse">
                                    Company
                                    <span ng-show="sortType == 'person.company' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'person.company' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'person.del_postcode'; sortReverse = !sortReverse">
                                    Del Postcode
                                    <span ng-show="sortType == 'person.del_postcode' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'person.del_postcode' && sortReverse" class="fa fa-caret-up"></span>
                                </th> 
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'status'; sortReverse = !sortReverse">
                                    Status
                                    <span ng-show="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span>                            
                                </th> 
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'delivery_date'; sortReverse = !sortReverse">
                                    Delivery Date
                                    <span ng-show="sortType == 'delivery_date' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'delivery_date' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'driver'; sortReverse = !sortReverse">
                                    Delivered By
                                    <span ng-show="sortType == 'driver' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'driver' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                                    Total Amount
                                    <span ng-show="sortType == 'total' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'total' && sortReverse" class="fa fa-caret-up"></span>                            
                                </th>                                        
                                 <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'pay_status'; sortReverse = !sortReverse">
                                    Payment
                                    <span ng-show="sortType == 'pay_status' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'pay_status' && sortReverse" class="fa fa-caret-up"></span>
                                </th>                                                                       
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'updated_by'; sortReverse = !sortReverse">
                                    Last Modified By
                                    <span ng-show="sortType == 'updated_by' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'updated_by' && sortReverse" class="fa fa-caret-up"></span>
                                </th> 
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'updated_at'; sortReverse = !sortReverse">
                                    Last Modified Time
                                    <span ng-show="sortType == 'updated_at' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-show="sortType == 'updated_at' && sortReverse" class="fa fa-caret-up"></span>
                                </th>                                                                                 
                                <th class="col-md-1 text-center">
                                    Action
                                </th>                                                                                                
                            </tr>
                            <tbody>
                                <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                    <td class="col-md-1 text-center">@{{ number }} </td>
                                    <td class="col-md-1 text-center">
                                        <a href="/transaction/@{{ transaction.id }}/edit">
                                            @{{ transaction.id }} 
                                        </a>
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.person.cust_id }} </td>                                
                                    <td class="col-md-1 text-center">
                                    <a href="/person/@{{ transaction.person.id }}">
                                    @{{ transaction.person.company }}
                                    </a>
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.person.del_postcode }}</td>

                                    {{-- status by color --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Pending'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: orange;" ng-if="transaction.status == 'Confirmed'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.status == 'Delivered'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: black;" ng-if="transaction.status == 'Verified Owe' || transaction.status == 'Verified Paid'">
                                        @{{ transaction.status }}
                                    </td>                                    
                                    <td class="col-md-1 text-center" ng-if="transaction.status == 'Cancelled'">
                                        <span style="color: white; background-color: red;" > @{{ transaction.status }} </span>
                                    </td>
                                    {{-- status by color ended --}}
                                    <td class="col-md-1 text-center">@{{ transaction.delivery_date }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.driver }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.total }}</td>
                                    {{-- pay status --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                        @{{ transaction.pay_status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                        @{{ transaction.pay_status }}
                                    </td>
                                    {{-- pay status ended --}}
                                    <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.updated_at}}</td>            
                                    <td class="col-md-1 text-center">
                                        {{-- print invoice         --}}
                                        <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                        {{-- button view shown when cancelled --}}
                                        <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>                                        
                                        {{-- <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-warning" ng-if="transaction.status != 'Cancelled'">Edit</a> --}}
                                        {{-- Payment Verification --}}
                                        @cannot('transaction_view')
                                        <a href="/transaction/status/@{{ transaction.id }}" class="btn btn-warning btn-sm" ng-if="transaction.status == 'Delivered' && transaction.pay_status == 'Owe'">Verify Owe</a>
                                        <a href="/transaction/status/@{{ transaction.id }}" class="btn btn-success btn-sm" ng-if="(transaction.status == 'Verified Owe' || transaction.status == 'Delivered') && transaction.pay_status == 'Paid'">Verify Paid</a>
                                        @endcannot
                                    </td>
                                </tr>
                                <tr ng-if="(transactions | filter:search).length == 0 || ! transactions.length">
                                    <td colspan="14" class="text-center">No Records Found</td>
                                </tr>                     
                            </tbody>
                        </table>
                    </div>           
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                </div>
        </div>
    </div>

    <script src="/js/transaction_index.js"></script>
    <script>
        $('#delfrom').datetimepicker({
            format: 'DD-MMMM-YYYY'
        });

        $('.select').select2({});
    </script>  
@stop