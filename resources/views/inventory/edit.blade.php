@inject('items', 'App\Item')
@inject('invs', 'App\InvRecord')

@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="invController">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="display_panel_title">
                    <h3 class="panel-title pull-left"><strong>Stock {{$inventory->type}}: {{$inventory->id}}</strong></h3>
                    <div class="pull-right">
                        <span><strong>Created On: {{Carbon\Carbon::parse($inventory->created_at)->format('d-m-Y')}}</strong></span>
                        <span style="padding-left: 30px;"><strong>Created By: {{$inventory->created_by}}</strong></span>
                        @if($inventory->updated_by)
                            <span style="padding-left: 30px;"><strong>Updated By: {{$inventory->updated_by}}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div style="padding: 0px 15px 0px 15px;">
                {!! Form::model($inventory,['id'=>'form_update', 'method'=>'PATCH','action'=>['InventoryController@update', $inventory->id]]) !!}
                    {!! Form::hidden('inventory_id', $inventory->id, ['id'=>'inventory_id','class'=>' form-control']) !!}

                <div class="form-group" style="padding-top: 20px;">
                    {!! Form::label('type', 'Action', ['class'=>'control-label']) !!}
                    {!! Form::select('type',
                        ['Incoming'=>'Stock In', 'Adjustment'=>'Adjustment'],
                        null,
                        [
                        'id'=>'type',
                        'class'=>'select form-control',
                        'disabled'=>'disabled'
                        ])
                    !!}
                </div>

                <div class="row">
                    @if($inventory->batch_num)
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                {!! Form::label('batch_num', 'Batch Num', ['class'=>'control-label']) !!}
                                {!! Form::text('batch_num', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="form-group">
                            {!! Form::label('rec_date', 'Receiving Date', ['class'=>'control-label']) !!}
                            <div class="input-group date">
                            {!! Form::text('rec_date', null, ['class'=>'form-control', 'id'=>'rec_date']) !!}
                            <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
                    {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'4']) !!}
                </div>

                <div class="table-responsive" style="padding-top: 30px;">

                    <table class="table table-list-search table-hover table-bordered table-condensed">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-5 text-center">
                                Item
                            </th>
                            <th class="col-md-1 text-center">
                                Current Qty
                            </th>
                            <th class="col-md-2 text-center">
                                Before Qty
                            </th>
                            <th class="col-md-2 text-center">
                                Incoming/Deducted Qty
                            </th>
                            <th class="col-md-2 text-center">
                                After Qty
                            </th>
                        </tr>

                        <tbody>
                            @unless(count($items)>0)
                                <td class="text-center" colspan="12">No Records Found</td>
                            @else

                                @foreach($items::orderBy('product_id')->get() as $item)
                                <tr class="form-group">
                                    <td class="col-md-5">
                                        {{$item->product_id}} - {{$item->name}} - {{$item->remark}}
                                    </td>
                                    <td class="col-md-1">
                                        <strong>{{$item->qty_now}}</strong>
                                    </td>
                                    <td class="col-md-2">
                                        <input type="text" name="current[{{$item->id}}]" value="{{$invs::whereItemId($item->id)->whereInventoryId($inventory->id)->first() ? $invs::whereItemId($item->id)->whereInventoryId($inventory->id)->first()->qtyrec_current : '-'}}" class="text-right form-control" readonly="readonly"/>
                                    </td>
                                    <td class="col-md-2">
                                        <input type="text" name="incoming[{{$item->id}}]" value="{{$invs::whereItemId($item->id)->whereInventoryId($inventory->id)->first() ? $invs::whereItemId($item->id)->whereInventoryId($inventory->id)->first()->qtyrec_incoming : '-'}}" class="text-right form-control"readonly="readonly"/>
                                    </td>
                                    <td class="col-md-2">
                                        <input type="text" name="incoming[{{$item->id}}]" value="{{$invs::whereItemId($item->id)->whereInventoryId($inventory->id)->first() ? $invs::whereItemId($item->id)->whereInventoryId($inventory->id)->first()->qtyrec_after : '-'}}" class="text-right form-control"readonly="readonly"/>
                                    </td>
                                </tr>
                                @endforeach

                            @endunless
{{--                             <tr ng-repeat="item in items" class="form-group">
                                <td class="col-md-5">
                                    @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>
                                    @{{item.qty_now}}
                                    </strong>
                                </td>
                                <td class="col-md-2">
                                    <strong>
                                        {!! Form::text('original[@{{item.id}}]', null, [
                                                        'class'=>'text-right form-control',
                                                        'ng-init'=>'originalModel=getOriginalInit(item.id) == null ? 0: getOriginalInit(item.id)',
                                                        'ng-model'=>'originalModel',
                                                        'readonly'=>'readonly'
                                                        ]) !!}

                                    </strong>
                                </td>
                                <td class="col-md-2">
                                    <strong>
                                        {!! Form::text('incoming[@{{item.id}}]', null, [
                                                        'class'=>'text-right form-control',
                                                        'ng-init'=>'incomingModel=getIncomingInit(item.id) == null ? 0: getIncomingInit(item.id)',
                                                        'ng-model'=>'incomingModel',
                                                        ]) !!}
                                    </strong>
                                </td>
                                <td class="col-md-2">
                                    <strong>
                                        {!! Form::text('after[@{{item.id}}]', null, [
                                                        'class'=>'text-right form-control',
                                                        'ng-model'=>'afterModel',
                                                        'ng-if'=>'compareModel(item.id, originalModel, incomingModel)',
                                                        'ng-value'=>'((+item.qty_now) + (+incomingModel - +originalModel)).toFixed(4)',
                                                        'readonly'=>'readonly'
                                                        ]) !!}
                                        {!! Form::text('after[@{{item.id}}]', null, [
                                                        'class'=>'text-right form-control',
                                                        'ng-model'=>'afterModel',
                                                        'ng-if'=>'!compareModel(item.id, originalModel, incomingModel)',
                                                        'ng-value'=>'0',
                                                        'readonly'=>'readonly'
                                                        ]) !!}
                                    </strong>
                                </td>
                            </tr>
                            <tr ng-if="items.length == 0 || ! items.length">
                                <td colspan="5" class="text-center">No Records Found!</td>
                            </tr> --}}
                        </tbody>
                    </table>
{{--
                    <div class="pull-left" style="margin-top:17px;">
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                    </div> --}}
                    <div class="pull-right" style="margin-top:17px;">
                        @cannot('transaction_view')
                        @cannot('account_view')
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary', 'form'=>'form_update', 'name'=>'form_update']) !!}
                        @endcannot
                        @endcannot
                        <a href="/item" class="btn btn-default">Cancel</a>
                    </div>

                {!! Form::close() !!}

                {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['InventoryController@destroy', $inventory->id], 'onsubmit'=>'return confirm("Are you sure you want to cancel the stock movement?")']) !!}
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.select').select2();
    $('.date').datetimepicker({
       format: 'YYYY-MM-DD',
       defaultDate: new Date(),
    });
</script>
<script src="/js/inv.js"></script>

@stop