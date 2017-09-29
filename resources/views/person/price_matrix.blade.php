@extends('template')
@section('title')
    {{ $PERSON_TITLE }}
@stop
@section('content')

@inject('custcategories', 'App\Custcategory')

<div class="create_edit" ng-app="app" ng-controller="priceMatrixController">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title">
                    <h3 class="panel-title"><strong>Price Matrix</strong></h3>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                {!! Form::open(['id'=>'search_form', 'method'=>'POST', 'action'=>['PriceController@getPriceMatrix']]) !!}
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('cust_id', 'Customer ID', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('cust_id',
                                request('cust_id') ? request('cust_id') : null,
                                [
                                    'class'=>'form-control input-sm',
                                    'placeholder'=>'ID',
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('custcategory_id', 'Cust Category', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('custcategory_id', [''=>'All']+$custcategories::orderBy('name')->pluck('name', 'id')->all(),
                                request('custcategory_id') ? request('custcategory_id') : '2',
                                ['class'=>'select form-control'])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('company', 'Cust ID Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('company',
                                request('company') ? request('company') : null,
                                [
                                    'class'=>'form-control input-sm',
                                    'placeholder'=>'Cust ID Name',
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('product_id', 'Product ID', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('product_id',
                                request('product_id') ? request('product_id') : null,
                                [
                                    'class'=>'form-control input-sm',
                                    'placeholder'=>'Product ID',
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('name', 'Product Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('name',
                                request('name') ? request('name') : null,
                                [
                                    'class'=>'form-control input-sm',
                                    'placeholder'=>'Name',
                                ])
                            !!}
                        </div>

                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('is_inventory', 'Product Type', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('is_inventory',
                                ['1'=>'Inventory Item', 'All'=>'All'],
                                request('is_inventory') ? request('is_inventory') : '1',
                                ['class'=>'select form-control'])
                            !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="btn-input-group">
                            <button type="submit" form="search_form" class="btn btn-default"><i class="fa fa-search"></i> <span class="hidden-xs">Search</span></button>
                            <button type="submit" form="submit_batch" class="btn btn-success"><i class="fa fa-check"></i> Batch Confirm</button>
                            <button type="submit" form="search_form" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> <span class="hidden-xs"> Export Excel</span></button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" id="exportable" style="padding-top:15px;">
                    <table class="table table-fixed table-list-search table-hover table-bordered" style="font-size: 12px;">
                        <thead>
                        <tr style="background-color: #DDFDF8;">
                            <td class="col-md-1 text-center">
                                <input type="checkbox" id="checkAll" />
                            </td>
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-1 text-center">
                                Cost Rate (%)
                            </th>
                            @foreach($items as $item)
                            <td class="col-md-1 text-left">
                                (<strong>{{$item->product_id}}</strong>) {{$item->name}}
                            </td>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($people as $person)
                        <tr>
                            <td class="col-md-1 text-center">
                                {!! Form::checkbox('checkbox[{{$item->id}} - {{$person->id}}]') !!}
                            </td>
                            <td>(<strong>{{$person->cust_id}}</strong>) {{$person->company}}</td>
                            <td class="col-md-1">
                                <input type="text" name="cost_rate[{{$person->id}}]" class="text-right" ng-value="{{$person->cost_rate}}" ng-model="costrate[{{$person->id}}]" style="width: 55px;">
                            </td>
                            @foreach($items as $item)
                                @php
                                    $price = \App\Price::where('person_id', $person->id)->where('item_id', $item->id)->first();
                                @endphp
                            <td class="col-md-1">
                                Retail Price
                                <input type="text" name="retail_price[{{$item->id}}-{{$person->id}}]" class="text-right" ng-value="{{$price ? $price->retail_price : ''}}" ng-model="retailprice[{{$item->id}}-{{$person->id}}]" ng-change="changeRetailPrice({{$item->id}}, {{$person->id}}, {{$price ? $price->retail_price : 0}})">
                                Quote Price
                                <input type="text" name="quote_price[{{$item->id}}-{{$person->id}}]" class="text-right" ng-value="{{$price ? $price->quote_price : ''}}" ng-model="quoteprice[$index]">
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        </tbody>
            {{--                 <tr dir-paginate="item in items | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController" pagination-id="item">
                                <td class="col-md-1 text-center">@{{ number }} </td>
                                <td class="col-md-1 text-center">@{{ item.product_id }}</td>
                                <td class="col-md-2">@{{ item.name }}</td>
                                <td class="col-md-1 text-center">@{{ item.unit }}</td>
                                <td class="col-md-1 text-right">
                                    <span ng-if="item.is_inventory === 1">
                                        <strong>@{{item.qty_now | currency: "": 4 }}</strong>
                                    </span>
                                    <span ng-if="item.is_inventory === 0">
                                        N/A
                                    </span>
                                </td>
                                <td class="col-md-1 text-right">
                                    <span ng-if="item.is_inventory === 1">
                                        <a href="/item/qtyorder/@{{item.id}}">@{{ item.qty_order ? item.qty_order : 0 | currency: "": 4 }}</a>
                                    </span>
                                    <span ng-if="item.is_inventory === 0">
                                        N/A
                                    </span>
                                </td>
                                <td class="col-md-1 text-right">@{{ item.lowest_limit | currency: "": 4 }}</td>
                                <td class="col-md-1 text-center">@{{ item.publish == 1 ? 'Yes':'No'  }}</td>
                                <td class="col-md-1 text-center">@{{ item.is_inventory == 1 ? 'Yes':'No'  }}</td>
                                <td class="col-md-1 text-center">@{{ item.is_active == 1 ? 'Yes':'No'  }}</td>
                                <td class="col-md-1 text-center">
                                    @cannot('transaction_view')
                                    <a href="/item/@{{ item.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                    @endcannot
                                </td>
                            </tr>
                            <tr ng-if="(items | filter:search).length == 0 || ! items.length">
                                <td class="text-center" colspan="10">No Records Found!</td>
                            </tr> --}}

                    </table>
                </div>
            {{--
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" pagination-id="item"> </dir-pagination-controls>
                    <label ng-if-"items" class="pull-right totalnum" for="totalnum">Showing @{{(items | filter:search).length}} of @{{items.length}} entries</label>
                </div> --}}
        </div>
    </div>
</div>

<script src="/js/price_matrix.js"></script>
@stop