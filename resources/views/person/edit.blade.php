@inject('freezers', 'App\Freezer')
@inject('accessories', 'App\Accessory')
@inject('items', 'App\Item')

@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

<div class="create_edit" style="margin-top:10px;" ng-app="app" ng-controller="personEditController">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Profile for {{$person->cust_id}} : {{$person->company}} </strong>
        -
        @if($person->active == 'Yes')
        [Active]
        @else
        [Inactive]
        @endif

            <a href="/person/log/{{$person->id}}" class="btn btn-warning pull-right">Log History</a>
        </h3>
    </div>

    <div class="panel-body">

        {!! Form::model($person,['id'=>'form_person', 'method'=>'PATCH','action'=>['PersonController@update', $person->id]]) !!}

            @include('person.form')

            <div class="col-md-12">
                <div class="pull-right">
                    @cannot('transaction_view')
                    {!! Form::submit('Edit Profile', ['class'=> 'btn btn-primary', 'form'=>'form_person']) !!}
                    @endcannot
        {!! Form::close() !!}

                    <a href="/person" class="btn btn-default">Cancel</a>
                </div>
                <div class="pull-left row">
                    <div class="col-md-5" style="margin-left: 3px">
                    @cannot('transaction_view')
                    @if($person->active == 'Yes')
                        {!! Form::submit('Deactivate', ['name'=>'active', 'class'=> 'btn btn-warning', 'form'=>'form_person']) !!}
                    @else
                        {!! Form::submit('Activate', ['name'=>'active', 'class'=> 'btn btn-success', 'form'=>'form_person']) !!}
                    @endif
                    @endcannot
                    </div>
                </div>
            </div>
    </div>
</div>
{{-- divider --}}
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Transaction History for {{$person->cust_id}} : {{$person->company}} </strong></h3>
    </div>

    <div class="panel-body">

    <div class="panel panel-default">
        <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_num">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
                          <option ng-value="10">10</option>
                          <option ng-value="30">30</option>
                          <option ng-value="70">70</option>
                          <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    {{-- <div class="pull-right">
                        <a href="/transaction/create" class="btn btn-success">+ New {{ $TRANS_TITLE }}</a>
                    </div> --}}
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="search_inv" class="search">Search Inv:</label>
                        <input type="text" ng-model="search.id" style="width:140px;">
                        <label for="search_status" class="search" style="padding-left: 10px">Status:</label>
                        <input type="text" ng-model="search.status" style="width:140px;">
                        <label for="search_payment" class="search" style="padding-left: 10px">Payment:</label>
                        <input type="text" ng-model="search.pay_status" style="width:140px;">
                    </div>
                </div>
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="search_updated_by" class="search">Last Modified By:</label>
                        <input type="text" ng-model="search.updated_by" style="width:140px;">
                        <label for="search_updated_by" class="search" style="padding-left: 10px">Last Modified Date:</label>
                        <input type="text" ng-model="search.updated_at" style="width:140px;">
                    </div>
                </div>
                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <label for="del_on" class="search">Delivery On:</label>
                        <input type="text" ng-model="search.delivery_date" style="width:140px;">
                        <label for="search_driver" class="search" style="padding-left: 10px;">Delivered By:</label>
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
                            <td colspan="10" class="text-center">No Records Found</td>
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
</div>
{{-- divider --}}
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                <h3 class="panel-title"><strong>Price Management : {{$person->company}}</strong></h3>
            </div>
        </div>
    </div>

    <div class="panel-body">
        {!! Form::model($price = new \App\Price, ['action'=>'PriceController@store']) !!}
        {!! Form::hidden('person_id', $person->id, ['id'=>'person_id']) !!}

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
                <tr ng-repeat="item in items" class="form-group">
                    <td class="col-md-8">
                        @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                    </td>
                    <td class="col-md-2">
                        <strong>
                            <input type="text" name="retail[@{{item.id}}]" class="text-right form-control" ng-init="retailModel = getRetailInit(item.id)" ng-model="retailModel" ng-change="getRetailChange(retailModel)"/>
                        </strong>
                    </td>
                    <td class="col-md-2">
                        <strong>
                            <input type="text" name="quote[@{{item.id}}]" class="text-right form-control" ng-init="quoteModel = getQuoteInit(item.id)" ng-model="quoteModel" ng-value="(+retailModel * personData.cost_rate/100).toFixed(2)"/>
                        </strong>
                    </td>
                </tr>
                <tr ng-if="items.length == 0 || ! items.length">
                    <td colspan="4" class="text-center">No Records Found!</td>
                </tr>
                </tbody>
            </table>
            <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>
            {!! Form::submit('Done', ['name'=>'done', 'class'=> 'btn btn-success pull-right', 'style'=>'margin-top:17px;']) !!}
        </div>
        {!! Form::close() !!}

    </div>
</div>
{{-- divider --}}
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>Freezer and Accessories : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                @include('person.edit_freezer')
            </div>

            <div class="col-md-6">
                @include('person.edit_accessory')
            </div>
        </div>
    </div>
</div>
{{-- divider --}}

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>File : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-7 text-center">
                    Path
                </th>
                <th class="col-md-2 text-center">
                    Upload On
                </th>
                <th class="col-md-2 text-center">
                    Action
                </th>
            </tr>

            <tbody>

                @unless(count($files)>0)
                <td class="text-center" colspan="7">No Records Found</td>
                @else
                    @foreach($files as $index => $file)
                    <tr>
                        <td class="col-md-1 text-center">{{ $index+1 }} </td>
                        <td class="col-md-7">
                        {!! Html::image($file->path, 'person asset', array( 'width' => 200, 'height' => 200 )) !!}
                            <a href="{{$file->path}}">
                            {!! str_replace("/person_asset/file/", "", "$file->path"); !!}
                            </a>
                        </td>
                        <td class="col-md-2 text-center">{{$file->created_at}}</td>
                        <td class="col-md-2 text-center">
                            {!! Form::open(['method'=>'DELETE', 'action'=>['PersonController@removeFile', $file->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                                {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                @endunless

            </tbody>
        </table>
    </div>

    <div class="panel-footer">
        {!! Form::open(['action'=>['PersonController@addFile', $person->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
        {!! Form::close() !!}
        <label class="pull-right totalnum" for="totalnum">
            Total of {{count($files)}} entries
        </label>
    </div>
</div>
{{-- divider --}}
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>Notes : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <div class="form-group">
            {!! Form::model($person, ['action'=>['PersonController@storeNote', $person->id]]) !!}
                {!! Form::label('note', 'Notes', ['class'=>'control-label']) !!}
                {!! Form::textarea('note', null, ['class'=>'form-control', 'rows'=>'3', 'ng-model'=>'noteModel']) !!}
                {!! Form::submit('Save', ['name'=>'save', 'class'=> 'btn btn-success pull-right', 'style'=>'margin-top:17px;']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
{{-- divider --}}
</div>
<script>
$(document).ready(function() {
    Dropzone.autoDiscover = false;
    $('.dropzone').dropzone({
        init: function()
        {
            this.on("complete", function()
            {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                location.reload();
              }
            });
        }

    });
});

$('.select').select2({
    placeholder:'Select...'
});
/*
    $('.selectCreate').select2({
        tags:true,

        createTag: function(newItem){

         return {
                    id: 'new:' + newItem.term,
                    text: newItem.term + ' [new]'
                };
        }

    }); */
</script>
<script src="/js/person_edit.js"></script>

@stop