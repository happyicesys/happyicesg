@inject('items', 'App\Item')
@inject('price', 'App\DtdPrice')
@inject('people', 'App\Person')

@extends('template')
@section('title')
    Setup
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/market/setup"><h1>Setup <i class="fa fa-cog"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app" ng-controller="setupController">
    <div class="panel-heading">
        <ul class="nav nav-pills nav-justified" role="tablist">
            @if(Auth::user()->hasRole('admin') or $people::where('user_id', Auth::user()->id)->first()->cust_type === 'OM')
                <li><a href="#member_price" role="tab" data-toggle="tab"> Member Price List</a></li>
                <li><a href="#cust_price" role="tab" data-toggle="tab"> D2D Customer Price List</a></li>
            @endif
            <li class="active"><a href="#postcode" role="tab" data-toggle="tab">Postcode Management</a></li>
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            {{-- first element --}}
            <div class="tab-pane" id="member_price">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="pull-left ">
                                <h4><strong>Price Management for DTD Members</strong></h4>
                            </div>
                            <div class="pull-right ">
                                {!! Form::submit('Done', ['class'=> 'btn btn-success', 'form'=>'done_price']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        {!! Form::model($price = new \App\DtdPrice, ['action'=>'MarketingController@storeSetupPrice', 'id'=>'done_price']) !!}

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered table-condensed">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-8 text-center">
                                        Item
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Retail Price ($)
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Quote Price ($)
                                    </th>
                                </tr>

                                <tbody>
                                    @unless(count($items)>0)
                                        <td class="text-center" colspan="7">No Records Found</td>
                                    @else

                                        @foreach($items::orderBy('product_id')->get() as $item)
                                        <tr class="form-group">
                                            <td class="col-md-8">
                                                {{$item->product_id}} - {{$item->name}} - {{$item->remark}}
                                            </td>
                                            <td class="col-md-2">
                                                <strong>
                                                    <input type="text" name="retail[{{$item->id}}]" value="{{$price::whereItemId($item->id)->first() ? $price::whereItemId($item->id)->first()->retail_price : '0'}}" class="text-right form-control"/>
                                                </strong>
                                            </td>
                                            <td class="col-md-2">
                                                <strong>
                                                    <input type="text" name="quote[{{$item->id}}]" value="{{$price::whereItemId($item->id)->first() ? $price::whereItemId($item->id)->first()->quote_price : '0'}}" class="text-right form-control"/>
                                                </strong>
                                            </td>
                                        </tr>
                                        @endforeach

                                    @endunless
                                </tbody>
                            </table>
                            <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>

                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
            {{-- end of first element--}}
            {{-- second element --}}
            <div class="tab-pane" id="cust_price">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage2" ng-init="itemsPerPage2='50'">
                                  <option>50</option>
                                  <option>100</option>
                                  <option>200</option>
                                </select>
                                <label for="display_num2" style="padding-right: 20px">per Page</label>
                            </div>

                            <div class="pull-right">
                                <a href="/market/setup/d2ditem/create" class="btn btn-success">+ Dtd online item</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('item', 'Item:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('item', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.item.product_id', 'placeholder'=>'Item']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('caption', 'Caption:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('caption', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.caption', 'placeholder'=>'Caption']) !!}
                            </div>
                        </div>

                        <div class="row"></div>

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-5 text-center">
                                        <a href="#" ng-click="sortType = 'item.product_id'; sortReverse = !sortReverse">
                                        Item
                                        <span ng-show="sortType == 'item.product_id' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'item.product_id' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-3 text-center">
                                        <a href="#" ng-click="sortType = 'caption'; sortReverse = !sortReverse">
                                        Caption
                                        <span ng-show="sortType == 'caption' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'caption' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'qty_divisor'; sortReverse = !sortReverse">
                                        Divisor
                                        <span ng-show="sortType == 'qty_divisor' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'qty_divisor' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Action
                                    </th>
                                </tr>

                                <tbody>
                                     <tr dir-paginate="salesitem in salesitems | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage2"
                                        pagination-id="salesitem"
                                        current-page="currentPage2"
                                        ng-controller="repeatController2"
                                    >
                                        <td class="col-md-1 text-center">
                                            @{{ number }}
                                        </td>
                                        <td class="col-md-5 text-center">
                                            @{{ salesitem.product_id }} -
                                            @{{ salesitem.item_name }}
                                        </td>
                                        <td class="col-md-3 text-center">
                                            @{{ salesitem.caption }}
                                        </td>
                                        <td class="col-md-1 text-center">
                                            @{{ salesitem.qty_divisor }}
                                        </td>
                                        <td class="col-md-2 text-center">
                                            <a href="/market/setup/d2ditem/@{{ salesitem.id }}/edit" class="btn btn-sm btn-primary">
                                            Edit</a>
                                        </td>
                                    </tr>
                                    <tr ng-show="(salesitems | filter:search).length == 0 || ! salesitems.length">
                                        <td colspan="14" class="text-center">No Records Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-footer">
                          <dir-pagination-controls pagination-id="salesitem" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                          <label class="pull-right totalnum" ng-if="salesitems" for="totalnum">Showing @{{(salesitems | filter:search).length}} of @{{salesitems.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of second element --}}
            {{-- third element --}}
            <div class="tab-pane active" id="postcode">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage" ng-init="itemsPerPage='50'">
                                  <option>50</option>
                                  <option>100</option>
                                  <option>150</option>
                                </select>
                                <label for="display_num" style="padding-right: 20px">per Page</label>
                            </div>

                            @if(Auth::user()->hasRole('admin'))
                                <div class="pull-right">
                                    {!! Form::open(['action'=>'MarketingController@storePostcode', 'files'=>true]) !!}
                                        {{ csrf_field() }}
                                        <div class="col-md-9 col-xs-6">
                                            {!! Form::label('postcode_excel', 'Import Postcodes (Excel)', ['class'=>'control-label']) !!}
                                            {!! Form::file('postcode_excel', null, ['class'=>'form-control']) !!}
                                        </div>
                                        <div class="col-md-3 col-xs-6" style="padding-top: 10px;">
                                            <button type="submit" class="btn btn-success">+ Import</button>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('area', 'Area', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('area', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.area_name', 'placeholder'=>'Area']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('group', 'AM', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('group', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.group', 'placeholder'=>'AM']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('postcode', 'Postcode:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('postcode', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.value', 'placeholder'=>'Postcode']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('manager', 'Manager:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('manager', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.person.name', 'placeholder'=>'Manager']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('street', 'Street:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('street', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.street', 'placeholder'=>'Street']) !!}
                            </div>
                        </div>

                        <label class="pull-right totalnum" for="totalnum">Showing @{{(postcodes | filter:search).length}} of @{{postcodes.length}} entries</label>

                        <div class="row">
                            <div style="padding: 20px 0px 10px 15px">
                                {!! Form::submit('Batch Update', ['name'=>'save', 'class'=> 'btn btn-success', 'form'=>'update_form']) !!}
                                @if(Auth::user()->hasRole('admin'))
                                    {!! Form::submit('Batch Delete', ['name'=>'delete', 'class'=> 'btn btn-danger', 'form'=>'update_form']) !!}
                                @endif
                            </div>
                        </div>

                        <div class="table-responsive">
                            {!! Form::open(['id'=>'update_form', 'method'=>'POST','action'=>['MarketingController@updatePostcodeForm']]) !!}
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        <input type="checkbox" id="checkAll" />
                                    </th>
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'area_code'; sortReverse = !sortReverse">
                                        Area Code
                                        <span ng-show="sortType == 'area_code' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'area_code' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'area_name'; sortReverse = !sortReverse">
                                        Area
                                        <span ng-show="sortType == 'area_name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'area_name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'group'; sortReverse = !sortReverse">
                                        AM
                                        <span ng-show="sortType == 'group' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'group' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'value'; sortReverse = !sortReverse">
                                        Postcode
                                        <span ng-show="sortType == 'value' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'value' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'block'; sortReverse = !sortReverse">
                                        Block
                                        <span ng-show="sortType == 'block' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'block' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        <a href="#" ng-click="sortType = 'street'; sortReverse = !sortReverse">
                                        Street
                                        <span ng-show="sortType == 'street' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'street' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        <a href="#" ng-click="sortType = 'person.name'; sortReverse = !sortReverse">
                                        Manager
                                        <span ng-show="sortType == 'person.name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'person.name' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                </tr>

                                <tbody>
                                     <tr dir-paginate="postcode in postcodes | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage" pagination-id="postcode" current-page="currentPage" ng-controller="repeatController">
                                        <td class="col-md-1 text-center"><input type="checkbox" name="checkbox[@{{postcode.id}}]" value="1" id="checkAll" /></td>
                                        <td class="col-md-1 text-center">@{{ number }} </td>
                                        <td class="col-md-1 text-center">@{{ postcode.area_code }}</td>
                                        <td class="col-md-1 text-center">@{{ postcode.area_name }}</td>
                                        <td class="col-md-1 text-center">@{{ postcode.group }}</td>
                                        <td class="col-md-1 text-center">@{{ postcode.value }}</td>
                                        <td class="col-md-1 text-center">@{{ postcode.block }}</td>
                                        <td class="col-md-2 text-center">@{{ postcode.street }}</td>
                                        <td class="col-md-2 text-center">
                                            <select ui-select2 name="manager[@{{postcode.id}}]" ng-model="person[postcode.id]" ng-init="person[postcode.id] = postcode.person_id">
                                                <option value=""></option>
                                                <option value="@{{member.id}}" ng-repeat="member in members">@{{member.name}}</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show="(postcodes | filter:search).length == 0 || ! postcodes.length">
                                        <td colspan="12" class="text-center">No Records Found</td>
                                    </tr>

                                </tbody>
                            </table>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="panel-footer">
                          <dir-pagination-controls pagination-id="postcode" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                    </div>
                </div>
            </div>
            {{-- end of third element --}}
        </div>
    </div>
</div>
{{--
<template id="postcode-template">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <form action="#" @submit.prevent="searchData" method="GET">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-2 col-xs-6">
                                <div class="form-group">
                                    <label for="area_name" class="control-label">Area</label>
                                    <input type="text" name="area_name" class="form-control" v-model="search.area_name" placeholder="Area">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="form-group">
                                    <label for="group" class="control-label">AM</label>
                                    <input type="text" name="group" class="form-control" v-model="search.group" placeholder="AM">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="form-group">
                                    <label for="postcode" class="control-label">Postcode</label>
                                    <input type="text" name="postcode" class="form-control" v-model="search.postcode" placeholder="Postcode">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="form-group">
                                    <label for="manager" class="control-label">Manager</label>
                                    <input type="text" name="manager" class="form-control" v-model="search.manager" placeholder="Manager">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <div class="form-group">
                                    <label for="street" class="control-label">Street</label>
                                    <input type="text" name="street" class="form-control" v-model="search.street" placeholder="Street">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-6 pull-right">
                                <div class="row">
                                    <div class="col-md-9 col-xs-10 pull-right text-center">
                                        <select2 v-model="selected_page">
                                            <option value="50">50 /page</option>
                                            <option value="100">100 /page</option>
                                            <option value="200">200 /page</option>
                                        </select2>
                                        <span style="margin-top: 5px; font-size: 15px;" v-if="pagination.total">
                                            @{{pagination.from}} - @{{pagination.to}} (@{{pagination.total}})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="pull-left" style="margin-bottom: 20px;">
                            <button type="submit" class="btn btn-default btn-md">
                                <i class="fa fa-search"></i>
                                <span class="hidden-xs"> Search</span>
                                <i class="fa fa-circle-o-notch fa-spin" v-if="searching"></i>
                            </button>
                            <a href="customer/create" class="btn btn-success btn-md">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-xs"> Create Customer</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr style="background-color: #a3a3c2;" class="inverse head">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" @click="sortBy('cust_id')">Customer ID</a>
                            <span v-if="sortkey == 'cust_id' && !reverse" class="fa fa-caret-down"></span>
                            <span v-if="sortkey == 'cust_id' && reverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" @click="sortBy('company')">Company</a>
                            <span v-if="sortkey == 'company' && !reverse" class="fa fa-caret-down"></span>
                            <span v-if="sortkey == 'company' && reverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" @click="sortBy('attn_name')">Name</a>
                            <span v-if="sortkey == 'attn_name' && !reverse" class="fa fa-caret-down"></span>
                            <span v-if="sortkey == 'attn_name' && reverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" @click="sortBy('contact')">Contact</a>
                            <span v-if="sortkey == 'contact' && !reverse" class="fa fa-caret-down"></span>
                            <span v-if="sortkey == 'contact' && reverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" @click="sortBy('email')">Email</a>
                            <span v-if="sortkey == 'email' && !reverse" class="fa fa-caret-down"></span>
                            <span v-if="sortkey == 'email' && reverse" class="fa fa-caret-up"></span>
                        </th>
                    </tr>

                    <tr v-for="(customer, index) in list" @click="redirectEdit(customer.id)" class="row_edit">
                        <td class="col-md-1 text-center">
                            @{{ index + pagination.from }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ customer.cust_id }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ customer.company }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ customer.attn_name }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ customer.contact }}
                            <span v-if="customer.alt_contact">/ @{{ customer.alt_contact }}</span>
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ customer.email }}
                        </td>
                    </tr>
                    <tr v-if="! pagination.total">
                        <td colspan="14" class="text-center"> No Results Found </td>
                    </tr>
                </table>
            </div>
            <div class="pull-left">
                <pagination :pagination="pagination" :callback="fetchTable" :offset="4"></pagination>
            </div>
        </div>
    </div>
</template> --}}

<script src="/js/setup.js"></script>
{{-- <script src="/js/setupController.js"></script> --}}
<script>
    $(function() {
        // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // save the latest tab; use cookies if you like 'em better:
            localStorage.setItem('lastTab', $(this).attr('href'));
        });

        // go to the latest tab, if it exists:
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });

    $('#checkAll').change(function(){
        var all = this;
        $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
    });
</script>
@stop