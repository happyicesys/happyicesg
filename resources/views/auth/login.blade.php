@extends('template')
@section('title')
Login
@stop
@section('content')

<style>
    .title {
        font-size: 96px;
        margin: 0;
        display: table;
        font-weight: 100;
        font-family: 'Lato';
        padding-top: 100px;
    }

    @media(min-width:1281px) {
        /*shift content to right and bottom*/
        .title{
            padding-left: 160px;
        }
    }
</style>

@if(auth()->guest())
<div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-2" style="padding-top: 60px;">
    <form method="POST" action="/auth/login">
        {!! csrf_field() !!}

        <div class="form-group row">
            <div class="col-md-2 col-sm-12 col-xs-12">
                <label for="login" class="control-label">Username or Email</label>
            </div>

            <div class="col-md-4 col-sm-12 col-xs-12">
                <input type="text" class="form-control" style="margin-top:5px;" name="login" value="{{ old('login') }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-2 col-sm-12 col-xs-12">
                <label for="email" class="control-label">Password</label>
            </div>

            <div class="col-md-4 col-sm-12 col-xs-12">
                <input type="password" class="form-control" name="password" id="password">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4 col-md-offset-2">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember">
                    </label>

                    <strong>Remember Me</strong>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4 col-md-offset-2">
                <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                    Login
                </button>

                <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
            </div>
        </div>
    </form>
</div>
@else
<div class="container">
    @if(date("H") < 12)
        <div class="title">
            <span class="col-xs-12">Good Morning</span>
            {{-- <span class="col-xs-12 text-center">{{$profile::lists('name')->first()}}</span> --}}
        </div>
    @elseif(date("H") > 11 && date("H") < 18)
        <div class="title">
            <span class="col-xs-12">Good Afternoon</span>
            {{-- <span class="col-xs-12 text-center">{{$profile::lists('name')->first()}}</span> --}}
        </div>
    @elseif(date("H") > 17)
        <div class="title">
            <span class="col-xs-12">Good Evening</span>
            {{-- <span class="col-xs-12 text-center">{{$profile::lists('name')->first()}}</span> --}}
        </div>
    @endif
</div>
@endif

@stop