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
                    <h3 class="panel-title pull-left"><strong>Stock {{$inventory->type}}</strong></h3>
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
                {!! Form::model($inventory,['method'=>'PATCH','action'=>['InventoryController@update', $inventory->id]]) !!}
                    {!! Form::hidden('inventory_id', $inventory->id, ['id'=>'inventory_id','class'=>' form-control']) !!}

                <div class="form-group" style="padding-top: 20px;">
                    {!! Form::label('type', 'Action', ['class'=>'control-label']) !!}
                    {!! Form::select('type',
                        ['Incoming'=>'Stock In', 'Adjustment'=>'Adjustment'],
                        null,
                        [
                        'id'=>'type',
                        'class'=>'select form-control',
                        'ng-model'=>'typeModel',
                        'ng-change'=>'typeModelChanged(typeModel)',
                        'disabled'=>'disabled'
                        ])
                    !!}
                </div>

                <div class="row">
                    @if($inventory->batch_num)
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group" ng-if="showBatch">
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
                    {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
                </div>

                <div class="table-responsive" style="padding-top: 30px;">

                    <table class="table table-list-search table-hover table-bordered table-condensed">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-4 text-center">
                                Item
                            </th>
                            <th class="col-md-2 text-center">
                                Current Qty
                            </th>
                            <th class="col-md-2 text-center">
                                Original Added Qty
                            </th>
                            <th class="col-md-2 text-center">
                                Adjust Incoming Qty
                            </th>
                            <th class="col-md-2 text-center">
                                After Qty
                            </th>
                        </tr>

                        <tbody>

                            @unless(count($invrecs)>0)
                            <td class="text-center" colspan="7">No Records Found</td>
                            @else
                            @foreach($invrecs as $invrec)
                            <tr class="txtMult form-group">
                                <td class="col-md-4">
                                    {{$invrec->item->product_id}} - {{$invrec->item->name}} - {{$invrec->item->remark}}
                                </td>
                                <td class="col-md-2">
                                    <input type="text" name="current[{{$invrec->id}}]" value="{{$invrec->item->qty_now}}" class="text-right currentClass form-control" readonly="readonly" />
                                </td>
                                <td class="col-md-2">
                                    <strong>
                                        <input type="text" name="original[{{$invrec->id}}]"  value="{{$invrec->qtyrec_current}}" class="text-right form-control" readonly="readonly" />
                                    </strong>
                                </td>
                                <td class="col-md-2">
                                    <strong>
                                        <input type="text" name="incoming[{{$invrec->id}}]"  value="{{$invrec->qtyrec_incoming}}" class="text-right incomingClass form-control"/>
                                    </strong>
                                </td>
                                <td class="col-md-2">
                                    <input type="text" name="after[{{$invrec->id}}]" class="text-right form-control afterClass" readonly="readonly"/>
                                </td>
                            </tr>
                            @endforeach
                            @endunless
                            <tr>
                                <td class="col-md-1 text-center">
                                    <strong>Grand Total</strong>
                                </td>
                                <td></td>
                                <td class="text-right" id="currentTotal" >
                                    <strong>
                                        <input type="text" name="total_current" value="{{$inventory->qtytotal_current}}" class="text-right form-control currentTotal" readonly="readonly" />
                                    </strong>
                                </td>
                                <td class="text-right" id="incomingTotal" >
                                    <strong>
                                        <input type="text" name="total_incoming" value="{{$inventory->qtytotal_incoming}}" class="text-right form-control incomingTotal" readonly="readonly" />
                                    </strong>
                                </td>
{{--                                 <td class="text-right" id="afterTotal" >
                                    <strong>
                                        <input type="text" name="total_after" value="{{$inventory->qtytotal_after}}" class="text-right form-control afterTotal" readonly="readonly" />
                                    </strong>
                                </td> --}}
                            </tr>
{{--
                        <tr ng-repeat="item in items" class="form-group">
                            <td class="col-md-6">
                                @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="current[@{{item.id}}]" class="text-right form-control" ng-init="currentModel = getCurrentInit(item.id)" ng-model="currentModel" />
                                </strong>
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="incoming[@{{item.id}}]" class="text-right form-control" ng-init="incomingModel = getIncomingInit(item.id)" ng-model="incomingModel"/>
                                </strong>
                            </td>
                            <td class="col-md-2">
                                <strong>
                                    <input type="text" name="after[@{{item.id}}]" class="text-right form-control" ng-init="afterModel = getAfterInit(item.id)" ng-model="afterModel" ng-value="(+currentModel + incomingModel).toFixed(4)"/>
                                </strong>
                            </td>
                        </tr>
                        <tr ng-if="items.length == 0 || ! items.length">
                            <td colspan="4" class="text-center">No Records Found!</td>
                        </tr> --}}

                        </tbody>
                    </table>

                    <div class="pull-left" style="margin-top:17px;">
                        {{-- <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete()">Delete</button> --}}
                    </div>
                    <div class="pull-right" style="margin-top:17px;">
                        @cannot('transaction_view')
                        @cannot('account_view')
                        {!! Form::submit('Edit', ['name'=>'done', 'class'=> 'btn btn-primary']) !!}
                        @endcannot
                        @endcannot
                        <a href="/item" class="btn btn-default">Cancel</a>
                    </div>

                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/inv.js"></script>
<script>
    $('.select').select2();
    $('.date').datetimepicker({
       format: 'YYYY-MM-DD',
       defaultDate: new Date(),
    });
</script>

@stop