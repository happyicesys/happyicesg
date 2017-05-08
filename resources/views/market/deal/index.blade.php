@inject('profiles', 'App\Profile')

@extends('template')
@section('title')
Deals
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/market/deal"><h1>Deals <i class="fa fa-wpforms"></i></h1></a>
    </div>

    <div class="panel panel-default" ng-app="app" ng-controller="transController" ng-cloak>
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

                <div class="pull-right">
                    <a href="/market/deal/create" class="btn btn-success">+ New Deal</a>
                    @if($commision_visible)
                        <a href="/market/deal/create/commision" class="btn btn-primary">+ New Commision</a>
                    @endif
                </div>

            </div>
        </div>

        <div class="panel-body">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('invoice', 'Invoice:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('invoice', null, ['class'=>'form-control input-sm', 'ng-model'=>'id', 'placeholder'=>'Inv Num']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('id', 'ID:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('id', null, ['class'=>'form-control input-sm', 'ng-model'=>'cust_id', 'placeholder'=>'Cust ID']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('company', 'ID Name:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company', null, ['class'=>'form-control input-sm', 'ng-model'=>'company', 'placeholder'=>'ID Name']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('status', 'Status:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('status', null, ['class'=>'form-control input-sm', 'ng-model'=>'status', 'placeholder'=>'Status']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('pay_status', 'Payment:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('pay_status', null, ['class'=>'form-control input-sm', 'ng-model'=>'pay_status', 'placeholder'=>'Payment']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('parent_name', 'Manager:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('parent_name', null, ['class'=>'form-control input-sm', 'ng-model'=>'parent_name', 'placeholder'=>'Manager']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('type', 'Type:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('type', null, ['class'=>'form-control input-sm', 'ng-model'=>'type', 'placeholder'=>'Type']) !!}
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('del_from', 'Delivery From:', ['class'=>'control-label search-title']) !!}
                    <div class="dropdown">
                        <a class="dropdown-toggle" id="dropdown1" role="button" data-toggle="dropdown" data-target="" href="">
                            <div class="input-group">
                                {!! Form::text('del_from', null, ['class'=>'form-control input-sm', 'ng-model'=>'del_from', 'ng-init'=>"del_from=weekstart", 'placeholder'=>'Delivery From']) !!}
                            </div>
                        </a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <datetimepicker data-ng-model="del_from" data-datetimepicker-config="{ dropdownSelector: '#dropdown1', minView: 'day'}"ng-change="dateChange(del_from)"/>
                        </ul>
                    </div>
                </div>

                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('del_to', 'Delivery To:', ['class'=>'control-label search-title']) !!}
                    <div class="dropdown">
                        <a class="dropdown-toggle" id="dropdown2" role="button" data-toggle="dropdown" data-target="" href="">
                            <div class="input-group">
                                {!! Form::text('del_to', null, ['class'=>'form-control input-sm', 'ng-model'=>'del_to', 'ng-init'=>"del_to=weekend", 'placeholder'=>'Delivery To']) !!}
                            </div>
                        </a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <datetimepicker data-ng-model="del_to" data-datetimepicker-config="{ dropdownSelector: '#dropdown2', minView: 'day'}" ng-change="dateChange2(del_to)"/>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 col-md-offset-3 col-sm-6 col-xs-12">
                    {!! Form::label('date_total', 'For Delivery Date from (@{{del_from}}) to (@{{del_to}})', ['class'=>'control-label']) !!}
                    <div class="row">
                        <div class="col-md-3 col-sm-3 col-xs-3" style="margin-left: 15px;">
                            Deal Total
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid">
                            @{{ totalDeal | currency: "": 2 }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-3 col-xs-3" style="margin-left: 15px;">
                            Commision Total
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 text-right" style="border: thin black solid">
                            @{{ totalComm | currency: "": 2 }}
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div style="padding: 0px 0px 10px 15px">
                    <button class="btn btn-default" ng-click="searchDB()">Search</button>
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
                                ID Name
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
                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                Manager
                                <span ng-if="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-if="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
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
                                <a href="" ng-click="sortType = 'type'; sortReverse = !sortReverse">
                                Type
                                <span ng-if="sortType == 'type' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-if="sortType == 'type' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                <td class="col-md-1 text-center">@{{ number }} </td>
                                <td class="col-md-1 text-center">
                                    <a href="/market/deal/@{{ transaction.id }}/edit">
                                        @{{ transaction.transaction_id ? transaction.transaction_id : 'Drf '+transaction.id }}
                                    </a>
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.person.cust_id }} </td>
                                <td class="col-md-1 text-center">
                                <a href="/person/@{{ transaction.person.id }}">
                                @{{ transaction.person.name }}
                                </a>
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.del_postcode ? transaction.del_postcode : transaction.person.del_postcode }}</td>
                                <td class="col-md-1 text-center">@{{ transaction.person.manager.name }}</td>

                                {{-- status by color --}}
                                <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Pending'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Draft'">
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
                                <td class="col-md-1 text-center" ng-if="transaction.status == 'Deleted'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>
                                <td class="col-md-1 text-center">
                                    @{{ transaction.gst ? transaction.total * 107/100 : transaction.total | currency: ""}}
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                                {{-- pay status --}}
                                <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                    @{{ transaction.pay_status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                    @{{ transaction.pay_status }}
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.type }} </td>
                                <td class="col-md-1 text-center">
                                    {{-- print invoice         --}}
                                    <a href="/market/deal/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                    {{-- button view shown when cancelled --}}
                                    <a href="/market/deal/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>
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

    <script src="/js/deal_index.js"></script>
@stop