@extends('template')
@section('title')
Freezer
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Editing {{$freezer->name}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($freezer,['method'=>'PATCH','action'=>['FreezerController@update', $freezer->id]]) !!}            

            @include('user.freezer.form')

            <div class="col-md-12">
                <div class="pull-right form_button_right">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
        {!! Form::close() !!}

                    <a href="/user" class="btn btn-default">Cancel</a>            
                </div>
                <div class="pull-left form_button_left">
                    {!! Form::open(['method'=>'DELETE', 'action'=>['FreezerController@destroy', $freezer->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>                
            </div>
    </div>
</div>
</div>

@stop