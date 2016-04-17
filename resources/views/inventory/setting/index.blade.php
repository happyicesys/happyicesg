@inject('items', 'App\item')

@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="invSettingController">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title">
                    <h3 class="panel-title"><strong>Inventory Qty Threshold Alert</strong></h3>
                </div>
            </div>
        </div>

        <div class="panel-body">
            {!! Form::model($item = new \App\Item, ['action'=>'InventoryController@invLowest']) !!}

            <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="padding-top: 10px;">
                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered table-condensed">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-9 text-center">
                                Item
                            </th>
                            <th class="col-md-3 text-center">
                                Lowest Boundary
                            </th>
                        </tr>

                        <tbody>

                            @unless(count($items)>0)
                                <td class="text-center" colspan="7">No Records Found</td>
                            @else

                                @foreach($items::orderBy('product_id')->get() as $item)
                                <tr class="form-group">
                                    <td class="col-md-9">
                                        {{$item->product_id}} - {{$item->name}} - {{$item->remark}}
                                    </td>
                                    <td class="col-md-3">
                                        <strong>
                                            <input type="text" name="lowest[{{$item->id}}]" value="{{$item->lowest_limit}}" class="text-right form-control"/>
                                        </strong>
                                    </td>
                                </tr>
                                @endforeach

                            @endunless

                        </tbody>
                    </table>
                </div>
                <div class="pull-right">
                    {!! Form::submit('Done', ['name'=>'confirm', 'class'=> 'btn btn-success']) !!}
                    <a href="/item" class="btn btn-default">Cancel</a>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>

@stop