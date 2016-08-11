@extends('template')

@section('title')

    Password Reset

@stop

@section('content')

<style type="text/css">

    body{
        padding-left: 0px;
    }

</style>


<div class="col-md-12 col-xs-12 create_edit">

    <div class="panel panel-primary">

        <div class="panel-heading">

            <h3 class="panel-title"><strong>Forgot Password?</strong></h3>

        </div>

        <div class="panel-body">

            {!! Form::open(['action'=>'Auth\AuthController@resetPassword']) !!}

            <div class="col-md-10 col-md-offset-1">

                <div class="form-group">

                    {!! Form::label('username', 'Username', ['class'=>'control-label']) !!}
                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                    {!! Form::text('username', null, ['class'=>'form-control']) !!}

                </div>

                <div class="form-group">

                    {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                    {!! Form::email('email', null, ['class'=>'form-control']) !!}

                </div>


                <div class="form-group pull-right create_edit">

                    {!! Form::submit('Reset Password', ['class'=> 'btn btn-success']) !!}

                    <a href="/auth/login" class="btn btn-default">Cancel</a>

                </div>

            </div>

            {!! Form::close() !!}

        </div>

    </div>

</div>

@stop