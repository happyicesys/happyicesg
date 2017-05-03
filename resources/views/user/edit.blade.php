@inject('person', 'App\Person')

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

    {!! Form::open(['id'=>'delete_user', 'method'=>'DELETE', 'action'=>['UserController@destroy', $user->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
    {!! Form::close() !!}

    <div class="panel-body">
        {!! Form::model($user,['id'=>'update_user', 'method'=>'PATCH','action'=>['UserController@update', $user->id]]) !!}
            @include('user.form', ['pass_text'=>'Leave blank to use same password'])
        {!! Form::close() !!}

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="input-group-btn">
                    <div class="pull-right">
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary', 'form'=>'update_user']) !!}
                        <a href="/user" class="btn btn-default">Cancel</a>
                    </div>
                    @if(Auth::user()->hasRole('admin') and !$person::where('user_id', $user->id)->first())
                        <div class="pull-left">
                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_user']) !!}
                                <button class="dropdown btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                Add to DTD
                                <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="/user/member/{{$user->id}}/om">Operation Manager (OM)</a></li>
                                    <li><a href="/user/member/{{$user->id}}/oe">Operation Executive (OE)</a></li>
                                    <li><a href="/user/member/{{$user->id}}/am">Area Manager (AM)</a></li>
                                    <li><a href="/user/member/{{$user->id}}/ab">Ambassador (AB)</a></li>
                                </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>

    .dropdown{
        position: absolute;
        z-index : 999;
    }

</style>

@stop