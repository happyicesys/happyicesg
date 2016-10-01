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
                <li><a href="#price" role="tab" data-toggle="tab"> Item Price List</a></li>
            @endif
            <li class="active"><a href="#postcode" role="tab" data-toggle="tab">Postcode Management</a></li>
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            {{-- first element --}}
            <div class="tab-pane" id="price">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="pull-left ">
                                <h4><strong>Price Management for DTD</strong></h4>
                            </div>
                            <div class="pull-right ">
                                {!! Form::submit('Done', ['class'=> 'btn btn-success', 'form'=>'done_price']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        {!! Form::model($price = new \App\DtdPrice, ['action'=>'MarketingController@storeSetupPrice', 'id'=>'done_price']) !!}
                        {{-- {!! Form::hidden('person_id', $person->id, ['id'=>'person_id']) !!} --}}

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
{{--                                 <tr ng-repeat="item in items" class="form-group">
                                    <td class="col-md-8">
                                        @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                                    </td>
                                    <td class="col-md-2">
                                        <strong>
                                            <input type="text" name="retail[@{{item.id}}]" class="text-right form-control" ng-init="retailModel=getRetailInit(item.id)" ng-model="retailModel" />
                                        </strong>
                                    </td>
                                    <td class="col-md-2">
                                        <strong>
                                            <input type="text" name="quote[@{{item.id}}]" class="text-right form-control" ng-init="quoteModel=getQuoteInit(item.id)" ng-model="quoteModel" />
                                        </strong>
                                    </td>
                                </tr>
                                <tr ng-if="items.length == 0 || ! items.length">
                                    <td colspan="4" class="text-center">No Records Found!</td>
                                </tr> --}}

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
                                {!! Form::label('group', 'Group', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('group', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.group', 'placeholder'=>'Group']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('postcode', 'Postcode:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('postcode', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.value', 'placeholder'=>'Postcode']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('manager', 'Manager:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('manager', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.person.name', 'placeholder'=>'Manager']) !!}
                            </div>
                        </div>

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
                                        Group
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
                          <label class="pull-right totalnum" for="totalnum">Showing @{{(postcodes | filter:search).length}} of @{{postcodes.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of second element --}}
        </div>
    </div>
</div>

<script src="/js/setup.js"></script>
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