@extends('template')
@section('title')
{{ $USER_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Editing {{$user->id}} : {{$user->name}} </strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($user,['method'=>'PATCH','action'=>['UserController@update', $user->id]]) !!}            

            @include('user.form', ['pass_text'=>'Leave blank to use same password'])

            <div class="col-md-12">
                <div class="pull-right form_button_right">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
        {!! Form::close() !!}

                    <a href="/user" class="btn btn-default">Cancel</a>            
                </div>
                <div class="pull-left form_button_left">
                    @can('delete_user')
                    {!! Form::open(['method'=>'DELETE', 'action'=>['UserController@destroy', $user->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                    @endcan
                </div>                
            </div>
    </div>
</div>
</div>

@stop