@inject('freezers', 'App\Freezer')
@inject('accessories', 'App\Accessory')
@inject('items', 'App\Item')
@inject('prices', 'App\Price')
@inject('dtdprice', 'App\DtdPrice')
@inject('people', 'App\Person')
@inject('priceTemplates', 'App\PriceTemplate')
@inject('outletVisits', 'App\OutletVisit')
@inject('vendings', 'App\Vending')

@extends('template')

<style>
    .panel {
        border-color: #6D3A9C !important;
    }

    .panel-heading {
        background-color: #6D3A9C !important;
        color: white;
    }

    .person-color {
        color:  #6D3A9C;
    }

    table {
        font-size: 14px;
    }
</style>

@section('title')
    {{ $PERSON_TITLE }}
@stop
@section('content')

<div class="create_edit" style="margin-top:10px;" ng-app="app" ng-controller="personEditController" ng-cloak>
    @php
        $disabled = false;
        $disabledStr = '';

        if(auth()->user()->hasRole('watcher') or auth()->user()->hasRole('subfranchisee') or auth()->user()->hasRole('hd_user') or auth()->user()->hasRole('event') or auth()->user()->hasRole('event_plus')) {
            $disabled = true;
            $disabledStr = 'disabled';
        }
    @endphp
                {{-- @php
                dd('here', $person->cust_id[0]);
            @endphp --}}
    @unless($person->cust_id[0] === 'H')
    <div class="panel">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-7 col-sm-7 col-xs-12">
                    <div class="form-group">
                    <span class="control-label">
                        <h3 class="panel-title"><strong>Profile for {{$person->cust_id}} : {{$person->company}} </strong>
                            -
                        @php
                            $statusStr = '';
                            switch($person->active) {
                                case 'Yes':
                                    $statusStr = '[Active]';
                                    break;
                                case 'No':
                                    $statusStr = '[Inactive]';
                                    break;
                                case 'Pending':
                                    $statusStr = '[Pending]';
                                    break;
                                case 'New':
                                    $statusStr = '[New]';
                                    break;
                            }
                        @endphp
                        {{$statusStr}}
                    </span>
                    </div>
                </div>

                <div class="col-md-5 col-sm-5 col-xs-12">
                    <div class="input-group-btn">
                        <div class="pull-right">
                            @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                {!! Form::submit('Create Transaction', ['class'=> 'btn btn-success', 'form'=>'person_transaction']) !!}
                                {{-- {!! Form::submit('Discard Item(s)', ['class'=> 'btn btn-danger', 'type'=>'button', 'name'=>'discard', 'form'=>'person_transaction']) !!} --}}
                                @if(!auth()->user()->hasRole('hd_user'))
                                @cannot('transaction_view')
                                    <a href="/person/replicate/{{$person->id}}" class="btn btn-default" onclick="return confirm('Are you sure to replicate?')">
                                        <i class="fa fa-files-o"></i> <span class="hidden-xs hidden-sm">Replicate</span>
                                    </a>
                                @endcannot
                                @endif
                                <a href="/person/log/{{$person->id}}" class="btn btn-warning">
                                    <i class="fa fa-history"></i> <span class="hidden-xs hidden-sm">Log History</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::open(['id'=>'person_transaction', 'method'=>'POST', 'action'=>['TransactionController@store']]) !!}
                {!! Form::hidden('person_id', $person->id) !!}
            {!! Form::close() !!}
        </div>

        <div class="panel-body">
            {!! Form::model($person,['id'=>'form_person', 'method'=>'PATCH','action'=>['PersonController@update', $person->id], 'onsubmit'=>'return storeDeliveryLatLng()']) !!}
                @include('person.form')
                @if(($person->is_vending === 1 or $person->is_dvm) and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee'))
                    @include('person.vending')
                @endif
                <div class="form-group">
                    {!! Form::label('price_template_id', 'Price Template', ['class'=>'control-label']) !!}
                    <select name="price_template_id" class="selectnotclear form-control" {{$disabled ? 'disabled' : ''}}>
                        <option value="-1">Customise Pricing</option>
                        @foreach($priceTemplates->all() as $priceTemplate)
                            <option value="{{ $priceTemplate->id }}" {{$person->price_template_id == $priceTemplate->id ? 'selected' : ''}}>
                                {{ $priceTemplate->name }}
                                @if($priceTemplate->remarks)
                                    ({{ $priceTemplate->remarks }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="input-group-btn">
                        <div class="pull-right">
                            @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            @cannot('transaction_view')
                            <button type="submit" class="btn btn-success" form="form_person">
                                Save Profile
                            </button>
                            {{-- {!! Form::submit('Save Profile', ['class'=> 'btn btn-success', 'form'=>'form_person']) !!} --}}
                            @endcannot
                            @endif
                            <a href="/person" class="btn btn-default">Back</a>
                        </div>
                        <div class="pull-left">
                            {{-- @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus')) --}}
                            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('supervisor') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('account'))
                            @cannot('transaction_view')
                                @if($person->active == 'Yes')
                                    {!! Form::submit('Pending', ['name'=>'active', 'class'=> 'btn btn-primary', 'form'=>'form_person']) !!}
                                    {!! Form::submit('Deactivate', ['name'=>'active', 'class'=> 'btn btn-warning', 'form'=>'form_person']) !!}
                                @elseif($person->active == 'Pending')
                                    {!! Form::submit('Activate', ['name'=>'active', 'class'=> 'btn btn-success', 'form'=>'form_person']) !!}
                                    {!! Form::submit('Deactivate', ['name'=>'active', 'class'=> 'btn btn-warning', 'form'=>'form_person']) !!}
                                @else
                                    <div class="btn-group">
                                        {!! Form::submit('Pending', ['name'=>'pending', 'class'=> 'btn btn-primary', 'form'=>'form_person']) !!}
                                        {!! Form::submit('Activate', ['name'=>'active', 'class'=> 'btn btn-success', 'form'=>'form_person']) !!}
                                        @if(Auth::user()->hasRole('admin'))
                                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_person']) !!}
                                        @endif
                                    </div>
                                @endif
                            @endcannot
                            @endif
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
<div class="panel">
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

<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                <h3 class="panel-title"><strong>Store Visit : {{$person->company}}</strong></h3>
            </div>
            <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target="#outletVisitModal" ng-click="onOutletVisitClicked($event)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
        </div>
    </div>

    <div class="panel-body">

        <div class="table-responsive">
            <table class="table table-list-search table-hover table-bordered table-condensed">
                <thead>
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            Date
                        </th>
                        <th class="col-md-1 text-center">
                            Day
                        </th>
                        <th class="col-md-1 text-center">
                            Outcome
                        </th>
                        <th class="col-md-3 text-center">
                            Remarks
                        </th>
                        <th class="col-md-2 text-center">
                            Created By
                        </th>
                        <th class="col-md-1 text-center">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="visit in outletvisitForm.person.outlet_visits">
                        <td class="col-md-1 text-center">
                            @{{ $index + 1 }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ visit.date }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ visit.day }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{outcomes[visit.outcome]}}
                        </td>
                        <td class="col-md-2 text-left">
                            @{{ visit.remarks }}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{ visit.creator.name }}
                        </td>
                        <td class="col-md-1 text-center">
                            <button class="btn btn-xs btn-danger btn-delete" ng-click="deleteOutletVisitEntry(visit.id)">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    <tr ng-if="!outletvisitForm.person.outlet_visits || outletvisitForm.person.outlet_visits.length == 0">
                        <td class="text-center" colspan="18">
                            No Results Found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- divider --}}
@if($person->franchisee_id and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee'))
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            <strong>F VendCash for {{$person->cust_id}} : {{$person->company}} </strong>
        </h3>
    </div>

    <div class="panel-body">
        @include('person.vendcash')
    </div>
</div>
@endif

{{-- divider --}}
@unless(Auth::user()->type === 'marketer')

    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title">
                    <h3 class="panel-title"><strong>Price Management : {{$person->company}}</strong></h3>
                </div>
            </div>
        </div>

        @if($person->priceTemplate()->exists())
            <div class="panel-body">

                @if($person->priceTemplate->attachments()->exists())
                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-9 text-center">
                                Path
                            </th>
                            <th class="col-md-2 text-center">
                                Action
                            </th>
                        </tr>

                        <tbody>
                            @foreach($person->priceTemplate->attachments as $index => $attachment)
                            <tr>
                                <td class="col-md-1 text-center">
                                    @{{$index + 1}}
                                </td>
                                <td class="col-md-9">
                                    <img src="{{$attachment->url}}" alt="{{$attachment->url}}" style="max-width:300px;">
                                </td>
                                <td class="col-md-2 text-center">
                                    <div class="btn-group">
                                        <a href="{{$attachment->url}}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-download"></i> <span class="hidden-xs">Open</span></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <label for="price_template">
                    Binded Price Template: {{$person->priceTemplate->name}} @if($person->priceTemplate->remarks) ({{$person->priceTemplate->remarks}}) @endif
                </label>
                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered table-condensed">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-8 text-center">
                                Item
                            </th>
                            <th class="col-md-2 text-center">
                                Retail Price ({{$person->profile->currency ? $person->profile->currency->symbol: '$'}})
                            </th>
                            <th class="col-md-2 text-center">
                                Quote Price ({{$person->profile->currency ? $person->profile->currency->symbol: '$'}})
                            </th>
                        </tr>

                        <tbody>
                            @foreach($person->priceTemplate->priceTemplateItems as $priceTemplateItem)
                            <tr class="form-group">
                                <td class="col-md-8">
                                    {{$priceTemplateItem->item->product_id}} - {{$priceTemplateItem->item->name}} - {{$priceTemplateItem->item->remark}}
                                </td>
                                <td class="col-md-2 text-right">
                                    <strong>
                                        {{$priceTemplateItem->retail_price}}
                                    </strong>
                                </td>
                                <td class="col-md-2 text-right">
                                    <strong>
                                        {{$priceTemplateItem->quote_price}}
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                            @if(count($person->priceTemplate->priceTemplateItems) == 0)
                            <tr>
                                <td colspan="4" class="text-center">No Records Found!</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                    <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>
                </div>
            </div>
        @else
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
                                Retail Price ({{$person->profile->currency ? $person->profile->currency->symbol: '$'}})
                            </th>
                            <th class="col-md-2 text-center">
                                Quote Price ({{$person->profile->currency ? $person->profile->currency->symbol: '$'}})
                            </th>
                        </tr>

                        <tbody>

                        @php
                            $disable = false;

                            if($person->cust_id[0] === 'H') {
                                $disable = false;
                            }

                            if($person->cust_type === 'OM' or $person->cust_type === 'OE' or $person->cust_type === 'AM' or $person->cust_type === 'AB') {
                                $disable = true;
                            }
                        @endphp

                        <tr ng-repeat="item in items" class="form-group">
                            <td class="col-md-8">
                                @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="retail[@{{item.item_id}}]" class="text-right form-control" ng-model="item.retail_price" ng-change="calQuotePrice($index, item)" {{$disable == true ? 'readonly' : ''}} {{$disabledStr}}/>
                                </strong>
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="quote[@{{item.item_id}}]" class="text-right form-control" ng-model="item.quote_price" {{$disable == true ? 'readonly' : ''}} {{$disabledStr}}/>
                                </strong>
                            </td>
                        </tr>
                        <tr ng-if="items.length == 0 || ! items.length">
                            <td colspan="4" class="text-center">No Records Found!</td>
                        </tr>
                        {{-- @endif --}}
                        </tbody>
                    </table>
                    <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>
                    @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        {!! Form::submit('Save Prices', ['name'=>'done', 'class'=> 'btn btn-success pull-right', 'style'=>'margin-top:17px;']) !!}
                    @endif
                </div>
                {!! Form::close() !!}
            </div>
        @endif
    </div>

@endunless

{{-- divider --}}
@if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
<div class="panel">
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
@endif
{{-- divider --}}

<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>File : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        {!! Form::open(['action'=>['PersonController@updateFilesName', $person->id]]) !!}
        <div class="table-responsive">
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
{{--                 <tr ng-repeat="file in files">
                    <td class="col-md-1 text-center">
                        @{{$index + 1}}
                    </td>
                    <td class="col-md-4">
                            <img src="@{{file.path}}" alt="@{{file.name}}" style="width:200px; height:200px;">
                            <embed src="@{{file.path}}" width="200" height="200" type='application/pdf' >
                    </td>
                    <td class="col-md-4">
                        <input type="text" class="form-control" name="file_name[@{{file.id}}]" ng-model="file.name">
                    </td>
                    <td class="col-md-2 text-center">
                        @{{file.created_at}}
                    </td>
                    <td class="col-md-1 text-center">
                        <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="removeFile(file.id)"><i class="fa fa-trash"></i> <span class="hidden-xs">Delete</span></a>
                        <a href="@{{file.path}}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> <span class="hidden-xs">Open</span></a>
                    </td>
                </tr>
                <tr ng-if="files.length == 0">
                    <td class="text-center" colspan="7">No Records Found</td>
                </tr> --}}
                @unless(count($files)>0)
                    <td class="text-center" colspan="7">No Records Found</td>
                @else
                    @foreach($files as $index => $file)
                    <tr>
                        <td class="col-md-1 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="col-md-4">
                            @php
                                $fileType = strtolower(end(explode(".",$file->path)));
                                // dd($fileType);
                                // $fileType = explode('/', $mimeType)[0];
                            @endphp
                            @if($fileType === 'pdf')
                                <embed src="{{$file->path}}" width="200" height="200" type="application/pdf">
                            @elseif($fileType === 'mov' or $fileType === 'mp4')
                                <span class="text-danger">
                                    (Video file) please open and watch >>
                                </span>

                                {{-- <embed src="{{$file->path}}" width="300" height="300" > --}}
                                {{-- <video src="{{$file->path}}" width="200" height="200" controls></video> --}}
                                {{-- <div class="embed-responsive embed-responsive-4by3"> --}}
                                    {{-- <iframe class="embed-responsive-item" src="{{$file->path}}"></iframe> --}}
                                {{-- </div> --}}
                            @else
                                <img src="{{$file->path}}" alt="{{$file->name}}" style="width:200px; height:200px;">
                            @endif
                        </td>
                        <td class="col-md-4">
                            {{-- <input type="text" class="form-control" name="file_name[{{$file->id}}]" value="{{$file->name}}" style="min-width: 300px;"> --}}
                            <textarea class="form-control" name="file_name[{{$file->id}}]" rows="5" style="min-width: 300px;">{{$file->name}}</textarea>
                        </td>
                        <td class="col-md-2 text-center">{{$file->created_at}}</td>
                        <td class="col-md-2 text-center">
                            @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                <button type="submit" name="removeFile" value="{{$file->id}}" onclick='return confirm("Are you sure you want to delete?")' class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> <span class="hidden-xs">Delete</span></button>
                            @endif
                            <a href="{{$file->path}}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> <span class="hidden-xs">Open</span></a>
                        </td>
                    </tr>
                    @endforeach
                @endunless
            </tbody>
        </table>
        </div>
        @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-check"></i> <span class="hidden-xs">Save Files Name</span></button>
        @endif
        {!! Form::close() !!}
{{--
        @if(count($files) > 0)
            {!! Form::open(['id'=>'remove_file', 'method'=>'DELETE', 'action'=>['PersonController@removeFile', $file->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
            {!! Form::close() !!}
        @endif --}}
    </div>

    <div class="panel-footer">
        @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
            {!! Form::open(['action'=>['PersonController@addFile', $person->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
            {!! Form::close() !!}
        @endif
        <label class="pull-right totalnum" for="totalnum">
            Total of @{{files.length}} entries
            {{-- Total of {{count($files)}} entries --}}
        </label>
    </div>
</div>
{{-- divider --}}
@if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>Maintenance Log : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            @include('person.maintenance_log')
        </div>
    </div>
</div>
@endif
{{-- divider --}}
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>Notes : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <div class="form-group">
            {!! Form::model($person, ['action'=>['PersonController@storeNote', $person->id]]) !!}
                {!! Form::label('note', 'Notes', ['class'=>'control-label']) !!}
                {!! Form::textarea('note', null, ['class'=>'form-control', 'rows'=>'3', 'ng-model'=>'noteModel', 'disabled'=>$disabled]) !!}
                @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                    {!! Form::submit('Save Note', ['name'=>'save', 'class'=> 'btn btn-success pull-right', 'style'=>'margin-top:17px;']) !!}
                @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>
{{-- divider --}}

<div class="modal fade" id="outletVisitModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">
                Outlet Visit "{{$person->cust_id}} - {{$person->company}}"
            </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            Date
                        </label>
                        <datepicker selector="form-control">
                            <input
                                type = "text"
                                name="chosen_date"
                                class = "form-control input-sm"
                                placeholder = "Visit Date"
                                ng-model = "outletvisitForm.date"
                                ng-change = "onOutletVisitDateChanged(outletvisitForm.date)"
                            />
                        </datepicker>
                    </div>
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            Day
                        </label>
                        <input type="text" name="name" class="form-control" ng-model="outletvisitForm.day" readonly>
                    </div>
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            Outcome
                        </label>
                        <select name="outcome" class="form-control select" ng-model="outletvisitForm.outcome">
                            @foreach($outletVisits::OUTCOMES as $index => $outcome)
                                <option value="{{$index}}" ng-init='outletvisitForm.outcome = "1"'>
                                    {{$outcome}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            Remarks
                        </label>
                        <textarea name="remarks" class="form-control" ng-model="outletvisitForm.remarks" rows="3"></textarea>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <button class="btn btn-success" ng-click="saveOutletVisitForm($event)"><i class="fa fa-upload"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script src="/js/person_edit.js"></script>
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

    function storeDeliveryLatLng() {
        var url = window.location.href;
        var location = '';

        if(url.includes("my")) {
            location = 'Malaysia';
        }else if(url.includes("sg")) {
            location = 'Singapore';
        }

        var dataObj = {
            del_postcode: $('#del_postcode').val(),
            del_address: $('#del_address').val(),
            country: location,
            person_id: $('#person_id').val()
        };
        if(dataObj.del_postcode || dataObj.del_address) {
            return retrieveLatLng(dataObj);
        }else {
            return true;
        }
    }

    function retrieveLatLng(dataObj) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode(
                        {componentRestrictions: {country: dataObj.country, postalCode: dataObj.del_postcode},
                        address: dataObj.del_address
                        }, function(results, status) {
            if(results[0]) {
                var data = JSON.parse(JSON.stringify(results[0].geometry.location));
                var coordObj = {
                    lat: data.lat,
                    lng: data.lng
                };
                axios.post('/api/person/storelatlng/' + dataObj.person_id, coordObj).then(function(response) {
                    return true;
                });
            }else {
                return true;
            }
        });
    }

</script>

@stop