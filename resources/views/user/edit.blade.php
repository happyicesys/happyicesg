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
                <div class="pull-left" style="padding-left: 200px;">
                    @can('delete_user')
                    {!! Form::open(['method'=>'DELETE', 'action'=>['UserController@destroy', $user->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                    @endcan
                    @cannot('transaction_view')
                    @cannot('supervisor_view')
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle pull-right" type="button" data-toggle="dropdown">
                        Add to DTD
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            @if(Auth::user()->hasRole('admin'))
                            <li><a href="/user/member/{{$user->id}}/om">Operation Manager (OM)</a></li>
                            <li><a href="/user/member/{{$user->id}}/oe">Operation Executive (OE)</a></li>
                            <li><a href="/user/member/{{$user->id}}/am">Area Manager (AM)</a></li>
                            <li><a href="/user/member/{{$user->id}}/ab">Ambassador (AB)</a></li>
                            @endif
                        </ul>
                    </div>
                    @endcannot
                    @endcannot
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