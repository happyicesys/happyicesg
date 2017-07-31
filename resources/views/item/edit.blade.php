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
    {{-- divider --}}
</div>

<script src="/js/item_edit.js"></script>

@stop