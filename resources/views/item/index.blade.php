@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/item"><h1> {{ $ITEM_TITLE }}<i class="fa fa-shopping-cart"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="itemController">

        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="pull-right">
                        @cannot('transaction_view')
                        <a href="/item/create" class="btn btn-success">+ New Product</a>
                        <a href="/inventory/create" class="btn btn-primary">+ Stock Movement</a>
                        <a href="/inventory/setting" class="btn btn-warning"><i class="fa fa-cog"></i> Limit Setting</a>
                        <a href="/inventory/email" class="btn btn-info"> Email Alert Limit Setting</a>
                        @endcannot
                    </div>
                </div>
            </div>

            <div class="panel-body">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <ul class="nav nav-pills nav-justified" role="tablist">
                            <li class="active"><a href="#item" role="tab" data-toggle="tab">Item</a></li>
                            @cannot('transaction_view')
                                <li><a href="#stock" role="tab" data-toggle="tab">Stock Movement</a></li>
                            @endcannot
                        </ul>
                    </div>

                    <div class="panel-body">
                        <div class="tab-content">
                            {{-- first element --}}
                            <div class="tab-pane active" id="item">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('product_id', 'ID:', ['class'=>'control-label search-title']) !!}
                                        {!! Form::text('product_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.product_id', 'placeholder'=>'ID']) !!}
                                    </div>
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('name', 'Product:', ['class'=>'control-label search-title']) !!}
                                        {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Product']) !!}
                                    </div>
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('remark', 'Desc:', ['class'=>'control-label search-title']) !!}
                                        {!! Form::text('remark', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.remark', 'placeholder'=>'Desc']) !!}
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12 row" style="padding-bottom: 10px;">
                                    <div class="pull-left display_panel_title">
                                        <label for="display_num">Display</label>
                                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='50'">
                                          <option ng-value="30">30</option>
                                          <option ng-value="50">50</option>
                                          <option ng-value="100">100</option>
                                          <option ng-value="All">All</option>
                                        </select>
                                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                                    </div>

                                    <div class="pull-right">
                                        <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                                    </div>
                                </div>

                                <div class="row"></div>

                                <div class="table-responsive" id="exportable">
                                    <table class="table table-list-search table-hover table-bordered">
                                        <tr style="background-color: #DDFDF8">
                                            <th class="col-md-1 text-center">
                                                #
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType = 'product_id'; sortReverse = !sortReverse">
                                                ID
                                                <span ng-if="sortType == 'product_id' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'product_id' && sortReverse" class="fa fa-caret-up"></span>
                                                </a>
                                            </th>
                                            <th class="col-md-2 text-center">
                                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                                Product
                                                <span ng-if="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                                </a>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType = 'unit'; sortReverse = !sortReverse">
                                                Unit
                                                <span ng-if="sortType == 'unit' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'unit' && sortReverse" class="fa fa-caret-up"></span>
                                                </a>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType = 'qty_now'; sortReverse = !sortReverse">
                                                Available Qty
                                                <span ng-if="sortType == 'qty_now' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'qty_now' && sortReverse" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType = 'qty_order'; sortReverse = !sortReverse">
                                                Booked Qty
                                                <span ng-if="sortType == 'qty_order' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'qty_order' && sortReverse" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType = 'lowest_limit'; sortReverse = !sortReverse">
                                                Threshold Limit
                                                <span ng-if="sortType == 'lowest_limit' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'lowest_limit' && sortReverse" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType = 'publish'; sortReverse = !sortReverse">
                                                E-comm
                                                <span ng-if="sortType == 'publish' && !sortReverse" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType == 'publish' && sortReverse" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                Action
                                            </th>
                                        </tr>

                                        <tbody>
                                            <tr dir-paginate="item in items | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController" pagination-id="item">
                                                <td class="col-md-1 text-center">@{{ number }} </td>
                                                <td class="col-md-1 text-center">@{{ item.product_id }}</td>
                                                <td class="col-md-2">@{{ item.name }}</td>
                                                <td class="col-md-1 text-center">@{{ item.unit }}</td>
                                                <td class="col-md-1 text-right"><strong>@{{ item.qty_now | currency: "": 4 }}</strong></td>
                                                <td class="col-md-1 text-right"><strong>@{{ item.qty_order ? item.qty_order : 0 | currency: "": 4 }}</strong></td>
                                                <td class="col-md-1 text-right">@{{ item.lowest_limit | currency: "": 4 }}</td>
                                                <td class="col-md-1 text-center">@{{ item.publish == 1 ? 'Yes':'No'  }}</td>
                                                <td class="col-md-1 text-center">
                                                    @cannot('transaction_view')
                                                    <a href="/item/@{{ item.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                                    @endcannot
                                                    {{-- disable due to inv implemented --}}
{{--
                                                    @cannot('accountant_view')
                                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(item.id)">Delete</button>
                                                    @endcannot --}}
                                                </td>
                                            </tr>
                                            <tr ng-if="(items | filter:search).length == 0 || ! items.length">
                                                <td class="text-center" colspan="10">No Records Found!</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" pagination-id="item"> </dir-pagination-controls>
                                    <label ng-if-"items" class="pull-right totalnum" for="totalnum">Showing @{{(items | filter:search).length}} of @{{items.length}} entries</label>
                                </div>
                            </div>
                            {{-- end of first element--}}

                            {{-- second element --}}
                            <div class="tab-pane" id="stock">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('id', 'ID:', ['class'=>'control-label search-title']) !!}
                                        {!! Form::text('id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.id', 'placeholder'=>'ID']) !!}
                                    </div>
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('type', 'Action:', ['class'=>'control-label search-title']) !!}
                                        {!! Form::text('type', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.type', 'placeholder'=>'Action']) !!}
                                    </div>
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('batch_num', 'Batch Num:', ['class'=>'control-label search-title']) !!}
                                        {!! Form::text('batch_num', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.batch_num', 'placeholder'=>'Batch Num']) !!}
                                    </div>
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('rec_date', 'Received On:', ['class'=>'control-label search-title']) !!}
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" id="dropdown4" role="button" data-toggle="dropdown" data-target="" href="">
                                                <div class="input-group">
                                                    {!! Form::text('rec_date', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.rec_date', 'placeholder'=>'Received On']) !!}
                                                </div>
                                            </a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                            <datetimepicker data-ng-model="search2.rec_date" data-datetimepicker-config="{ dropdownSelector: '#dropdown4', minView: 'day'}" ng-change="dateChange3(search2.rec_date)"/>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                        {!! Form::label('created_at', 'Created On:', ['class'=>'control-label search-title']) !!}
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" id="dropdown3" role="button" data-toggle="dropdown" data-target="" href="">
                                                <div class="input-group">
                                                    {!! Form::text('created_at', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.created_at', 'placeholder'=>'Created On']) !!}
                                                </div>
                                            </a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                            <datetimepicker data-ng-model="search2.created_at" data-datetimepicker-config="{ dropdownSelector: '#dropdown3', minView: 'day'}" ng-change="dateChange2(search2.created_at)"/>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12 row">
                                    <div class="pull-left display_panel_title">
                                        <label for="display_num">Display</label>
                                        <select ng-model="itemsPerPage2" ng-init="itemsPerPage2='50'">
                                          <option ng-value="30">30</option>
                                          <option ng-value="50">50</option>
                                          <option ng-value="100">100</option>
                                          <option ng-value="All">All</option>
                                        </select>
                                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                                    </div>
                                </div>

                                <div class="row"></div>

                                <div class="table-responsive">
                                    <table class="table table-list-search table-hover table-bordered">
                                        <tr style="background-color: #DDFDF8">
                                            <th class="col-md-1 text-center">
                                                #
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType2 = 'id'; sortReverse2 = !sortReverse2">
                                                ID
                                                <span ng-if="sortType2 == 'id' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'id' && sortReverse2" class="fa fa-caret-up"></span>
                                                </a>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType2 = 'type'; sortReverse2 = !sortReverse2">
                                                Action
                                                <span ng-if="sortType2 == 'type' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'type' && sortReverse2" class="fa fa-caret-up"></span>
                                                </a>
                                            </th>
                                            <th class="col-md-2 text-center">
                                                <a href="" ng-click="sortType2 = 'batch_num'; sortReverse2 = !sortReverse2">
                                                Batch Num
                                                <span ng-if="sortType2 == 'batch_num' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'batch_num' && sortReverse2" class="fa fa-caret-up"></span>
                                            </th>
                                             <th class="col-md-2 text-center">
                                                <a href="" ng-click="sortType2 = 'remark'; sortReverse2 = !sortReverse2">
                                                Remark
                                                <span ng-if="sortType2 == 'remark' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'remark' && sortReverse2" class="fa fa-caret-up"></span>
                                                </a>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType2 = 'rec_date'; sortReverse2 = !sortReverse2">
                                                Received On
                                                <span ng-if="sortType2 == 'rec_date' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'rec_date' && sortReverse2" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-2 text-center">
                                                <a href="" ng-click="sortType2 = 'created_at'; sortReverse2 = !sortReverse2">
                                                Created On
                                                <span ng-if="sortType2 == 'created_at' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'created_at' && sortReverse2" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                <a href="" ng-click="sortType2 = 'created_by'; sortReverse2 = !sortReverse2">
                                                Created By
                                                <span ng-if="sortType2 == 'created_by' && !sortReverse2" class="fa fa-caret-down"></span>
                                                <span ng-if="sortType2 == 'created_by' && sortReverse2" class="fa fa-caret-up"></span>
                                            </th>
                                            <th class="col-md-1 text-center">
                                                Action
                                            </th>
                                        </tr>

                                        <tbody>
                                            <tr dir-paginate="inventory in inventories | filter:search2 | orderBy:sortType2:sortReverse2 | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController" pagination-id="inventory">
                                                <td class="col-md-1 text-center">@{{ number }} </td>
                                                <td class="col-md-1 text-center">@{{ inventory.id }}</td>
                                                <td class="col-md-1 text-center">@{{ inventory.type }}</td>
                                                <td class="col-md-2 text-center">@{{ inventory.batch_num ? inventory.batch_num : '-' }}</td>
                                                <td class="col-md-2 text-center">@{{ inventory.remark }}</td>
                                                <td class="col-md-2 text-center">@{{ inventory.rec_date }}</td>
                                                <td class="col-md-2 text-center">@{{ inventory.created_at }}</td>
                                                <td class="col-md-1 text-center">@{{ inventory.created_by }}</td>
                                                <td class="col-md-1 text-center">
                                                    <a href="/inventory/@{{ inventory.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                                </td>
                                            </tr>
                                            <tr ng-if="(inventories | filter:search2).length == 0 || ! inventories.length">
                                                <td class="text-center" colspan="9">No Records Found!</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" pagination-id="inventory"> </dir-pagination-controls>
                                    <label ng-if-"inventories" class="pull-right totalnum" for="totalnum">Showing @{{(inventories | filter:search).length}} of @{{inventories.length}} entries</label>
                                </div>
                            </div>
                            {{-- end of second element --}}

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="/js/item.js"></script>
@stop