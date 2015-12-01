@extends('template')
@section('title')
Role
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Editing {{$role->id}} : {{$role->name}} </strong></h3>
    </div>

    <div class="panel-body">

        {!! Form::model($role,['method'=>'PATCH','action'=>['RoleController@update', $role->id]]) !!}    

            @include('user.role.form')

            <div class="col-md-12">
                <div class="pull-right form_button_right">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
        {!! Form::close() !!}

                    <a href="/user" class="btn btn-default">Cancel</a>            
                </div>
                <div class="pull-left form_button_left">
                    @can('delete_role')
                    {!! Form::open(['method'=>'DELETE', 'action'=>['RoleController@destroy', $role->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                    @endcan
                </div>                
            </div>
    </div>
</div>
</div>

@stop