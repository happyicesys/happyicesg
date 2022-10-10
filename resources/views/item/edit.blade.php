@inject('uoms', 'App\Uom')

@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="itemController">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>Editing {{$item->product_id}} : {{$item->name}} </strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($item,['method'=>'PATCH','action'=>['ItemController@update', $item->id], 'files'=>true]) !!}
            {!! Form::hidden('item_id', $item->id, ['id'=>'item_id']) !!}
            {!! Form::hidden('image_remain', $item->img_remain, ['id'=>'img_remain']) !!}

                @include('item.form')

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="pull-right" >
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
            {!! Form::close() !!}

                        <a href="/item" class="btn btn-default">Cancel</a>
                    </div>
                    <div class="pull-left">
                        @if($item->is_active == '0')
                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_form']) !!}
                            {!! Form::submit('Activate', ['class'=> 'btn btn-success', 'form'=>'active_form']) !!}
                        @else
                            {!! Form::submit('Deactivate', ['class'=> 'btn btn-warning', 'form'=>'active_form']) !!}
                        @endif
                    </div>
                </div>
        </div>
    </div>

    {!! Form::open(['id'=>'delete_form', 'method'=>'DELETE', 'action'=>['ItemController@destroy', $item->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
    {!! Form::close() !!}

    {!! Form::open(['id'=>'active_form', 'method'=>'POST', 'action'=>['ItemController@setActiveState', $item->id], 'onsubmit'=>'return confirm("Are you sure you want to activate/deactivate the item?")']) !!}
    {!! Form::close() !!}

    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="pull-left">
                    <strong>
                        UOM: {{$item->product_id}} - {{$item->name}}
                    </strong>
                </span>
                <button class="btn btn-success pull-right" ng-click="onItemUomCreateClicked($event)" data-toggle="modal" data-target="#uom-modal">
                    <i class="fa fa-plus"></i>
                    Create
                </button>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-3 text-center">
                            UOM
                        </th>
                        <th class="col-md-3 text-center">
                            Value
                        </th>
                        <th class="col-md-3 text-center">
                            Label
                        </th>
                        <th class="col-md-2"></th>
                    </tr>
                    <tbody>
                        <tr ng-repeat="itemUom in itemUoms">
                            <td class="col-md-1 text-center">
                                @{{ $index + 1 }}
                            </td>
                            <td class="col-md-3 text-center">
                                @{{ itemUom.uom.name }}
                            </td>
                            <td class="col-md-3 text-right">
                                @{{ itemUom.value }} @{{ baseItemUom.uom.name }}
                            </td>
                            <td class="col-md-3 text-center">
                                <span ng-if="itemUom.is_base_unit" class="badge badge-primary" style="background-color: #296192">
                                    Base UOM
                                </span>
                                <span ng-if="itemUom.is_transacted_unit" class="badge badge-success" style="background-color: #5ab55a;">
                                    Transacted UOM
                                </span>
                            </td>
                            <td class="col-md-2 text-center">
                                <div class="btn-group">
                                    <a href="" class="btn btn-default btn-sm" ng-click="onItemUomEditClicked(itemUom)" data-toggle="modal" data-target="#uom-modal">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                    <a href="" class="btn btn-danger btn-sm" ng-click="onItemUomDeleteClicked(itemUom)">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr ng-if="!itemUoms || itemUoms.length == 0">
                            <td colspan="24" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- divider --}}
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <h3 class="panel-title"><strong>Photos : {{$item->name}}</strong></h3>
            </div>
        </div>

        <div class="panel-body">
        {!! Form::open(['id'=>'edit_caption', 'action'=>['ItemController@editCaption', $item->id]]) !!}
            <div class="table-responsive">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-4 text-center">
                        Photo
                    </th>
                    <th class="col-md-6 text-center">
                        Caption
                    </th>
                    <th class="col-md-1 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                    <tr ng-repeat="image in images" class="form-group">
                        <td class="col-md-1 text-center">
                            @{{ $index + 1 }}
                        </td>
                        <td class="col-md-4">
                            <a href="@{{image.path}}">
                                <img ng-src="@{{image.path}}" height="250" width="250">
                            </a>
                        </td>
                        <td class="col-md-6 text-center">
                            <input type="text" name="caption[@{{image.id}}]" class="form-control" ng-init="captionModel = getCaptionInit(image.id)" ng-model="captionModel"/>
                        </td>
                        <td class="col-md-1 text-center">
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(image.id)">Delete</button>
                        </td>
                    </tr>

                    <tr ng-if="images.length == 0 || ! images.length">
                        <td colspan="4" class="text-center">No Records Found!</td>
                    </tr>
                </tbody>
            </table>
            </div>
            {!! Form::submit('Done', ['class'=> 'btn btn-success pull-right', 'form'=>'edit_caption']) !!}
        </div>
        {!! Form::close() !!}

        <div class="panel-footer">
            {!! Form::open(['action'=>['ItemController@addImage', $item->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
            {!! Form::close() !!}


            <label class="pull-right totalnum" for="totalnum">
                Total of @{{imageLength}}/ 4 images uploaded
            </label>
        </div>
    </div>

    <div id="uom-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                      @{{formUom.id ? 'Edit UOM' : 'New UOM'}}
                      <span ng-if="form.id">
                        @{{formUom.name}}
                      </span>
                      for {{$item->product_id}} - {{$item->name}}
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="uom_id">
                            UOM Name
                        </label>
                        <div ng-show="!formUom.id">
                            <select ng-model="formUom.uom_id" class="select form-control" >
                                <option value=""></option>
                                @foreach($uoms::whereNotIn('id', $item->itemUoms->lists('uom_id'))->orderBy('sequence', 'asc')->get() as $uom)
                                    <option value="{{$uom->id}}">
                                        {{$uom->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" class="form-control" ng-model="formUom.uom.name" ng-if="formUom.id" disabled>
                    </div>
                  <div class="form-group">
                    <label for="template-name">
                        Value
                    </label>
                    <input type="number" class="form-control" ng-model="formUom.value" ng-disabled="formUom.is_base_unit">
                </div>
                  <div class="form-inline">
                    <label>
                        <input type="checkbox" ng-model="formUom.is_base_unit" ng-change="onIsBaseUnitChecked()" ng-true-value=true ng-false-value=false>
                        <span style="padding-left: 5px; margin-top: 5px;">
                            Base UOM? <small>(override other Base UOM)</small>
                        </span>
                    </label>

                    <label style="padding-left: 10px;">
                        <input type="checkbox" ng-model="formUom.is_transacted_unit" ng-true-value=true ng-false-value=false>
                        <span style="padding-left: 5px; margin-top: 5px;">
                            Transacted UOM? <small>(override other Transacted UOM)</small>
                        </span>
                    </label>
                  </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" data-dismiss="modal" ng-click="onFormUomSaveClicked($event)" ng-disabled="!formUom.uom_id && !formUom.value">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/item_edit.js"></script>

@stop