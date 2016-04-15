@inject('items', 'App\Item')

@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="invController">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title">
                    <h3 class="panel-title"><strong>Stock Movement</strong></h3>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div style="padding: 0px 15px 0px 15px;">
                {!! Form::model($inventory = new \App\Inventory, ['action'=>'InventoryController@store']) !!}

                <div class="form-group" style="padding-top: 20px;">
                    {!! Form::label('type', 'Action', ['class'=>'control-label']) !!}
                    {!! Form::select('type',
                        ['Incoming'=>'Stock In', 'Adjustment'=>'Adjustment'],
                        null,
                        [
                        'id'=>'type',
                        'class'=>'select form-control',
                        'ng-model'=>'typeModel',
                        'ng-change'=>'typeModelChanged(typeModel)'
                        ])
                    !!}
                </div>

                <div class="form-group" ng-if="showBatch">
                    {!! Form::label('batch_num', 'Batch Num', ['class'=>'control-label']) !!}
                    {!! Form::text('batch_num', null, ['class'=>'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
                    {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
                </div>

                <div class="table-responsive" style="padding-top: 30px;">

                    <table class="table table-list-search table-hover table-bordered table-condensed">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-6 text-center">
                                Item
                            </th>
                            <th class="col-md-2 text-center">
                                Current Qty
                            </th>
                            <th class="col-md-2 text-center">
                                Incoming Qty
                            </th>
                            <th class="col-md-2 text-center">
                                After Qty
                            </th>
                        </tr>

                        <tbody>

                            @unless(count($items)>0)
                            <td class="text-center" colspan="7">No Records Found</td>
                            @else
                            @foreach($items::orderBy('product_id')->get() as $item)
                            <tr class="txtMult form-group">
                                <td class="col-md-6">
                                    {{$item->product_id}} - {{$item->name}} - {{$item->remark}}
                                </td>
                                <td class="col-md-2">
                                    <input type="text" name="current[{{$item->id}}]" value="{{$item->qty_now + 0}}" class="text-right currentClass form-control" readonly="readonly" />
                                </td>
                                <td class="col-md-2">
                                    <input type="text" name="incoming[{{$item->id}}]" class="text-right incomingClass form-control"/>
                                </td>
                                <td class="col-md-2">
                                    <input type="text" name="after[{{$item->id}}]" class="text-right form-control afterClass" readonly="readonly"/>
                                </td>
                            </tr>
                            @endforeach
                            @endunless
                            <tr>
                                <td class="col-md-1 text-center"><strong>Total</strong></td>
                                    <td class="text-right" id="currentTotal" >
                                        <strong>
                                            <input type="text" name="total_create" class="text-right form-control currentTotal" readonly="readonly" />
                                        </strong>
                                    </td>
                                    <td class="text-right" id="incomingTotal" >
                                        <strong>
                                            <input type="text" name="total_create" class="text-right form-control incomingTotal" readonly="readonly" />
                                        </strong>
                                    </td>
                                    <td class="text-right" id="afterTotal" >
                                        <strong>
                                            <input type="text" name="total_create" class="text-right form-control afterTotal" readonly="readonly" />
                                        </strong>
                                    </td>
                            </tr>

                        </tbody>
                    </table>

                    <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>
                    <div class="pull-right" style="margin-top:17px;">
                        {!! Form::submit('Done', ['name'=>'done', 'class'=> 'btn btn-success']) !!}
                        <a href="/item" class="btn btn-default">Cancel</a>
                    </div>

                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/inv_create.js"></script>
<script>
    $('.select').select2();
</script>

@stop