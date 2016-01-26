@extends('template')
@section('title')
Login
@stop
@section('content')

<div class="col-md-10 col-md-offset-2" style="padding-top: 50px">
<form method="POST" action="/auth/login">
    {!! csrf_field() !!}

    <div class="form-group row">
        <div class="col-md-2">
            <label for="login" class="control-label">Username or Email</label>
        </div>

        <div class="col-md-4">
            <input type="text" class="form-control" style="margin-top:5px;" name="login" value="{{ old('login') }}">
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            <label for="email" class="control-label">Password</label>
        </div>

        <div class="col-md-4">
            <input type="password" class="form-control" name="password" id="password">
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2 col-md-offset-2">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2 col-md-offset-2">
            <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                Login
            </button>
        </div>
    </div>
</form>
</div>

@stop