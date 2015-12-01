@extends('template')
@section('title')
Position
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New Position</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($position = new \App\Position, ['action'=>'PositionController@store']) !!}

            @include('position.form')

            <div class="col-md-12">
                <div class="form-group pull-right">
                    {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                    <a href="/position" class="btn btn-default">Cancel</a>            
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

@stop