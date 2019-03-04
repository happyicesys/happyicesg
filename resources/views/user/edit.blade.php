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

    {!! Form::open(['id'=>'user_activation', 'method'=>'POST', 'action'=>['UserController@userActivationControl', $user->id]]) !!}
    {!! Form::close() !!}

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

                            @if($user->is_active)
                                {!! Form::submit('Deactivate', ['class'=> 'btn btn-warning', 'form'=>'user_activation']) !!}
                            @else
                                {!! Form::submit('Activate', ['class'=> 'btn btn-warning', 'form'=>'user_activation']) !!}
                                {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_user']) !!}
                            @endif
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

@if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('operation'))
    <div class="panel panel-primary">
        <div class="panel-heading">
            Profile Access : {{$user->id}} - {{$user->name}}
        </div>
        <div class="panel-body">
            <assignProfile id="assignProfileController" :user_id={{json_encode($user->id)}} inline-template>
                @include('user._assignProfile')
            </assignProfile>
        </div>
    </div>
@endif
</div>

<style>
    .dropdown{
        position: absolute;
        z-index : 999;
    }
</style>
<script src="/js/vue-controller/assignProfileController.js"></script>

@stop