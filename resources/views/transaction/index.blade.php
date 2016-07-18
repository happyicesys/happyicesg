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
                                'ng-model'=>'search.name'])
                            !!}
                        </div>
                    </div>
                    <div class="pull-right">
                        <a href="/transaction/create" class="btn btn-success">+ New {{ $TRANS_TITLE }}</a>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('invoice', 'Invoice:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('invoice', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.id', 'placeholder'=>'Inv Num']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('id', 'ID:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.cust_id', 'placeholder'=>'Cust ID']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('company', 'Company:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('company', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.company', 'placeholder'=>'Company']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('status', 'Status:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('status', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.status', 'placeholder'=>'Status']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('pay_status', 'Payment:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('pay_status', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.pay_status', 'placeholder'=>'Payment']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('updated_by', 'Last Modify By:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('updated_by', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.updated_by', 'placeholder'=>'Last Modified By']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('created_at', 'Last Modify Dt:', ['class'=>'control-label search-title']) !!}
                        <div class="dropdown">
                            <a class="dropdown-toggle" id="dropdown3" role="button" data-toggle="dropdown" data-target="" href="">
                                <div class="input-group">
                                    {!! Form::text('created_at', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.updated_at', 'placeholder'=>'Last Modify Date']) !!}
                                </div>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <datetimepicker data-ng-model="search.updated_at" data-datetimepicker-config="{ dropdownSelector: '#dropdown3', minView: 'day'}" ng-change="dateChange2(search.updated_at)"/>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('delivery_date', 'Delivery On:', ['class'=>'control-label search-title']) !!}
                        <div class="dropdown">
                            <a class="dropdown-toggle" id="dropdown2" role="button" data-toggle="dropdown" data-target="" href="">
                                <div class="input-group">
                                    {!! Form::text('delivery_date', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.delivery_date', 'ng-init'=>"search.delivery_date=today", 'placeholder'=>'Delivery Date']) !!}
                                </div>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <datetimepicker data-ng-model="search.delivery_date" data-datetimepicker-config="{ dropdownSelector: '#dropdown2', minView: 'day'}" ng-change="dateChange(search.delivery_date)"/>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('driver', 'Delivered By:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('driver', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.driver', 'placeholder'=>'Delivered By']) !!}
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
                                    <span ng-if="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'cust_id'; sortReverse = !sortReverse">
                                    ID
                                    <span ng-if="sortType == 'cust_id' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'cust_id' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'company'; sortReverse = !sortReverse">
                                    Company
                                    <span ng-if="sortType == 'company' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'company' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'del_postcode'; sortReverse = !sortReverse">
                                    Del Postcode
                                    <span ng-if="sortType == 'del_postcode' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'del_postcode' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'status'; sortReverse = !sortReverse">
                                    Status
                                    <span ng-if="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'delivery_date'; sortReverse = !sortReverse">
                                    Delivery Date
                                    <span ng-if="sortType == 'delivery_date' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'delivery_date' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'driver'; sortReverse = !sortReverse">
                                    Delivered By
                                    <span ng-if="sortType == 'driver' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'driver' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'total'; sortReverse = !sortReverse">
                                    Total Amount
                                    <span ng-if="sortType == 'total' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'total' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'total_qty'; sortReverse = !sortReverse">
                                    Total Qty
                                    <span ng-if="sortType == 'total_qty' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'total_qty' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                 <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'pay_status'; sortReverse = !sortReverse">
                                    Payment
                                    <span ng-if="sortType == 'pay_status' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'pay_status' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'updated_by'; sortReverse = !sortReverse">
                                    Last Modified By
                                    <span ng-if="sortType == 'updated_by' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'updated_by' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortType = 'updated_at'; sortReverse = !sortReverse">
                                    Last Modified Time
                                    <span ng-if="sortType == 'updated_at' && !sortReverse" class="fa fa-caret-down"></span>
                                    <span ng-if="sortType == 'updated_at' && sortReverse" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    Action
                                </th>
                            </tr>
                            <tbody>
                                <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage track by $index" current-page="currentPage" ng-controller="repeatController">
                                    <td class="col-md-1 text-center">@{{ number }} </td>
                                    <td class="col-md-1 text-center">
                                        <a href="/transaction/@{{ transaction.id }}/edit">
                                            @{{ transaction.id }}
                                        </a>
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td>
                                    <td class="col-md-1 text-center">
                                    <a href="/person/@{{ transaction.person_id }}">
                                    @{{ transaction.company }}
                                    </a>
                                    </td>
                                    <td class="col-md-1 text-center">@{{ transaction.del_postcode }}</td>

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
                                    <td class="col-md-1 text-center" style="color: black; background-color:orange;" ng-if="transaction.status == 'Verified Owe'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: black; background-color:green;" ng-if="transaction.status == 'Verified Paid'">
                                        @{{ transaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" ng-if="transaction.status == 'Cancelled'">
                                        <span style="color: white; background-color: red;" > @{{ transaction.status }} </span>
                                    </td>
                                    {{-- status by color ended --}}
                                    <td class="col-md-1 text-center">@{{ transaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.driver }}</td>

                                    <td class="col-md-1 text-center" ng-if="transaction.gst">@{{ (+(transaction.total * 7/100).toFixed(2) + transaction.total * 1).toFixed(2)}}</td>
                                    <td class="col-md-1 text-center" ng-if="!transaction.gst">@{{ transaction.total }}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                                    {{-- pay status --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                        @{{ transaction.pay_status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                        @{{ transaction.pay_status }}
                                    </td>
                                    {{-- pay status ended --}}
                                    <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                                    <td class="col-md-1 text-center">@{{ transaction.updated_at }}</td>
                                    <td class="col-md-1 text-center">
                                        {{-- print invoice         --}}
                                        <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                        {{-- button view shown when cancelled --}}
                                        <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>

                                        {{-- Payment Verification --}}
{{--                                         @cannot('supervisor_view')
                                        @cannot('transaction_view')
                                        <a href="/transaction/status/@{{ transaction.id }}" class="btn btn-warning btn-sm" ng-if="transaction.status == 'Delivered' && transaction.pay_status == 'Owe'">Verify Owe</a>
                                        <a href="/transaction/status/@{{ transaction.id }}" class="btn btn-success btn-sm" ng-if="(transaction.status == 'Verified Owe' || transaction.status == 'Delivered') && transaction.pay_status == 'Paid'">Verify Paid</a>
                                        @endcannot
                                        @endcannot --}}
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