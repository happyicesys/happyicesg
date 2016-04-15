@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>New {{ $ITEM_TITLE }}</strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($item = new \App\Item, ['action'=>'ItemController@store']) !!}

                @include('item.form')

                <div class="col-md-12">
                    <div class="form-group pull-right" style="padding: 20px 95px 0px 0px">
                        {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                        <a href="/item" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@stop