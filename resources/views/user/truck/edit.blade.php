@extends('template')
@section('title')
Truck
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Editing {{$truck->name}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($truck,['id'=>'edit_truck', 'method'=>'PATCH','action'=>['TruckController@update', $truck->id]]) !!}
            @include('user.truck.form')
        {!! Form::close() !!}

            <div class="col-md-12 col-xs-12">
                <div class="row">
                    <div class="input-group-btn">
                        <div class="pull-left">
                            {!! Form::open(['method'=>'DELETE', 'action'=>['TruckController@destroy', $truck->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                                {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="pull-right">
                            {!! Form::submit('Edit', ['class'=> 'btn btn-primary', 'form'=>'edit_truck']) !!}
                            <a href="/user" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>

@stop