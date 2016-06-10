@extends('template')
@section('title')
Customers
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/market/customer"><h1>Customers <i class="fa fa-male"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="customerController">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_panel_title">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='50'">
                            <option ng-value="10">10</option>
                            <option ng-value="30">30</option>
                            <option ng-value="50">50</option>
                            <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        <a href="/market/customer/create" class="btn btn-success">+ New Customer</a>
                        <a href="/market/customer/batchcreate" class="btn btn-primary">+ Batch Create Customer</a>
                        @if(Auth::user()->hasRole('admin'))
                            <a href="/market/customer/emaildraft" class="btn btn-warning">Customer Email Draft</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('cust_id', 'ID:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('cust_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.cust_id', 'placeholder'=>'ID']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('contact', 'Contact:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('contact', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.contact', 'placeholder'=>'Contact']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('block', 'Block:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('block', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.block', 'placeholder'=>'Block']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('floor', 'Floor:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('floor', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.floor', 'placeholder'=>'Floor']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('unit', 'Unit:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('unit', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.unit', 'placeholder'=>'Unit']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('active', 'Active:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('active', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.active', 'placeholder'=>'Active']) !!}
                    </div>
                </div>

                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                    </div>
                </div>
                <div class="table-responsive" id="exportable">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'cust_id'; sortReverse = !sortReverse">
                                ID
                                <span ng-show="sortType == 'cust_id' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'cust_id' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                Name
                                <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'contact'; sortReverse = !sortReverse">
                                Contact
                                <span ng-show="sortType == 'contact' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'contact' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'block'; sortReverse = !sortReverse">
                                Block
                                <span ng-show="sortType == 'block' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'block' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'floor'; sortReverse = !sortReverse">
                                Floor
                                <span ng-show="sortType == 'floor' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'floor' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'unit'; sortReverse = !sortReverse">
                                Unit
                                <span ng-show="sortType == 'unit' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'unit' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                Delivery Address
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'del_postcode'; sortReverse = !sortReverse">
                                Postcode
                                <span ng-show="sortType == 'del_postcode' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'del_postcode' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'active'; sortReverse = !sortReverse">
                                Active
                                <span ng-show="sortType == 'active' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'active' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                        </tr>

                        <tbody>
                            <tr dir-paginate="customer in customers | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                <td class="col-md-1 text-center">@{{ number }} </td>
                                <td class="col-md-1 text-center">
                                    <a href="/market/customer/@{{ customer.id }}/edit">
                                    @{{ customer.cust_id }}</td>
                                    </a>
                                <td class="col-md-1">@{{ customer.name }}</td>
                                <td class="col-md-1">
                                    @{{ customer.contact }}
                                    <span ng-show="customer.alt_contact.length > 0">
                                    / @{{ customer.alt_contact }}
                                    </span>
                                </td>
                                <td class="col-md-1">@{{ customer.block }}</td>
                                <td class="col-md-1">@{{ customer.floor }}</td>
                                <td class="col-md-1">@{{ customer.unit }}</td>
                                <td class="col-md-2">@{{ customer.del_address }}</td>
                                <td class="col-md-1 text-center">@{{ customer.del_postcode }}</td>
                                <td class="col-md-1 text-center">@{{ customer.active }}</td>
                            </tr>
                            <tr ng-if="(customers | filter:search).length == 0 || ! customers.length">
                                <td colspan="9" class="text-center">No Records Found</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label ng-if="customers" class="pull-right totalnum" for="totalnum">Showing @{{(customers | filter:search).length}} of @{{customers.length}} entries</label>
                </div>
        </div>
    </div>

    <script src="/js/customer.js"></script>
@stop