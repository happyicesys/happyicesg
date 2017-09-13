@inject('freezers', 'App\Freezer')
@inject('accessories', 'App\Accessory')
@inject('items', 'App\Item')
@inject('prices', 'App\Price')
@inject('dtdprice', 'App\DtdPrice')
@inject('people', 'App\Person')

@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

<div class="create_edit" style="margin-top:10px;" ng-app="app" ng-controller="personEditController" ng-cloak>
    @unless($person->cust_id[0] === 'D' or $person->cust_id[0] === 'H')
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-7 col-sm-7 col-xs-12">
                    <div class="form-group">
                    <span class="control-label">
                        <h3 class="panel-title"><strong>Profile for {{$person->cust_id}} : {{$person->company}} </strong>
                            -
                        @if($person->active == 'Yes')
                            [Active]
                        @else
                            [Inactive]
                        @endif
                    </span>
                    </div>
                </div>

                <div class="col-md-5 col-sm-5 col-xs-12">
                    <div class="input-group-btn">
                        <div class="pull-right">
                            {!! Form::submit('Create Transaction', ['class'=> 'btn btn-success', 'form'=>'person_transaction']) !!}
                            @cannot('transaction_view')
                                <a href="/person/replicate/{{$person->id}}" class="btn btn-default" onclick="return confirm('Are you sure to replicate?')">
                                    <i class="fa fa-files-o"></i> <span class="hidden-xs hidden-sm">Replicate</span>
                                </a>
                            @endcannot
                            <a href="/person/log/{{$person->id}}" class="btn btn-warning">
                                <i class="fa fa-history"></i> <span class="hidden-xs hidden-sm">Log History</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::open(['id'=>'person_transaction', 'method'=>'POST', 'action'=>['TransactionController@store']]) !!}
                {!! Form::hidden('person_id', $person->id) !!}
            {!! Form::close() !!}
            </h3>
        </div>

        <div class="panel-body">
            {!! Form::model($person,['id'=>'form_person', 'method'=>'PATCH','action'=>['PersonController@update', $person->id]]) !!}
                    @include('person.form')

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="input-group-btn">
                        <div class="pull-right">
                            @cannot('transaction_view')
                            {!! Form::submit('Edit Profile', ['class'=> 'btn btn-primary', 'form'=>'form_person']) !!}
                            @endcannot
                            <a href="/person" class="btn btn-default">Cancel</a>
                        </div>
                        <div class="pull-left">
                            @cannot('transaction_view')
                                @if($person->active == 'Yes')
                                    {!! Form::submit('Deactivate', ['name'=>'active', 'class'=> 'btn btn-warning', 'form'=>'form_person']) !!}
                                @else
                                    <div class="btn-group">
                                        {!! Form::submit('Activate', ['name'=>'active', 'class'=> 'btn btn-success', 'form'=>'form_person']) !!}
                                        @if(Auth::user()->hasRole('admin'))
                                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_person']) !!}
                                        @endif
                                    </div>
                                @endif
                            @endcannot
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

            {!! Form::open(['id'=>'delete_person', 'method'=>'DELETE', 'action'=>['PersonController@destroy', $person->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    @endunless

{{-- divider --}}
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title">
            <strong>Transaction History for {{$person->cust_id}} : {{$person->company}} </strong>
            @if($person->cust_id[0] === 'D' or $person->cust_id[0] === 'H')
                <a href="/person/log/{{$person->id}}" class="btn btn-warning pull-right">Log History</a>
            @endif
        </h3>
    </div>

    <div class="panel-body">
        @include('person.transaction_history')
    </div>
</div>
{{-- divider --}}
@unless(Auth::user()->type === 'marketer')

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
                    @if($person->cust_type === 'OM' or $person->cust_type === 'OE' or $person->cust_type === 'AM' or $person->cust_type === 'AB')

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
                                        <input type="text" name="retail[{{$item->id}}]" value="{{$dtdprice::whereItemId($item->id)->first() ? $dtdprice::whereItemId($item->id)->first()->retail_price : '0'}}" class="text-right form-control" readonly/>
                                    </strong>
                                </td>
                                <td class="col-md-2">
                                    <strong>
                                        <input type="text" name="quote[{{$item->id}}]" value="{{$dtdprice::whereItemId($item->id)->first() ? $dtdprice::whereItemId($item->id)->first()->quote_price : '0'}}" class="text-right form-control" readonly/>
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        @endunless
                    @elseif($person->cust_id[0] === 'H')
                        <tr ng-repeat="item in items" class="form-group">
                            <td class="col-md-8">
                                @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="retail[@{{item.id}}]" class="text-right form-control" ng-init="retailModel = getRetailInit(item.id)" ng-model="retailModel" readonly/>
                                </strong>
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="quote[@{{item.id}}]" class="text-right form-control" ng-init="quoteModel = getQuoteInit(item.id)" ng-model="quoteModel" ng-value="(+retailModel * personData.cost_rate/100).toFixed(2)" readonly/>
                                </strong>
                            </td>
                        </tr>
                        <tr ng-if="items.length == 0 || ! items.length">
                            <td colspan="4" class="text-center">No Records Found!</td>
                        </tr>
                    @else
                    <tr ng-repeat="item in items" class="form-group">
                        <td class="col-md-8">
                            @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                        </td>
                        <td class="col-md-2">
                            <strong>
                                <input type="text" name="retail[@{{item.id}}]" class="text-right form-control" ng-init="retailModel = getRetailInit(item.id)" ng-model="retailModel" />
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
                    @endif
                    </tbody>
                </table>
                <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>
                {!! Form::submit('Done', ['name'=>'done', 'class'=> 'btn btn-success pull-right', 'style'=>'margin-top:17px;']) !!}
            </div>
            {!! Form::close() !!}

        </div>
    </div>

@endunless

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
        {!! Form::open(['id'=>'update_names', 'action'=>['PersonController@updateFilesName', $person->id]]) !!}
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-4 text-center">
                    Path
                </th>
                <th class="col-md-4 text-center">
                    Name
                </th>
                <th class="col-md-2 text-center">
                    Upload On
                </th>
                <th class="col-md-1 text-center">
                    Action
                </th>
            </tr>

            <tbody>
                <tr ng-repeat="file in files">
                    <td class="col-md-1 text-center">
                        @{{$index + 1}}
                    </td>
                    <td class="col-md-4">
                        <a href="@{{file.path}}">
                            <img src="@{{file.path}}" alt="@{{file.name}}" style="width:200px; height:200px;">
                        </a>
                    </td>
                    <td class="col-md-4">
                        <input type="text" class="form-control" name="file_name[@{{file.id}}]" ng-model="file.name">
                    </td>
                    <td class="col-md-2 text-center">
                        @{{file.created_at}}
                    </td>
                    <td class="col-md-1 text-center">
                        <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="removeFile(file.id)"><i class="fa fa-trash"></i> <span class="hidden-xs">Delete</span></a>
                    </td>
                </tr>
                <tr ng-if="files.length == 0">
                    <td class="text-center" colspan="7">No Records Found</td>
                </tr>
{{--                 @unless(count($files)>0)
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
                @endunless --}}

            </tbody>
        </table>
        {!! Form::close() !!}

        <button type="submit" class="btn btn-success pull-right" form="update_names"><i class="fa fa-check"></i> <span class="hidden-xs">Update All Files Name</span></button>
    </div>

    <div class="panel-footer">
        {!! Form::open(['action'=>['PersonController@addFile', $person->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
        {!! Form::close() !!}
        <label class="pull-right totalnum" for="totalnum">
            Total of @{{files.length}} entries
            {{-- Total of {{count($files)}} entries --}}
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

</script>
<script src="/js/person_edit.js"></script>

@stop