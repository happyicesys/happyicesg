@extends('template')
@section('title')
Vending Machine
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="itemController">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>Editing {{$vending->vend_id}} : {{$vending->serial_no}} </strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($vending,['method'=>'PATCH','action'=>['VMController@updateVending', $vending->id]]) !!}

                @include('vm.form')

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="pull-right" >
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
            {!! Form::close() !!}

                        <a href="/vm" class="btn btn-default">Cancel</a>
                    </div>
                </div>
        </div>
    </div>

</div>

@stop