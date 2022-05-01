@extends('template')
@section('title')
Service
@stop
@section('content')

<div class="create_edit">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>Editing Service Job for {{$serviceItem->transaction->id}} <br> ({{$serviceItem->transaction->person->cust_id}} - {{$serviceItem->transaction->person->company}})</strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($serviceItem,['method'=>'PATCH','action'=>['TransactionController@updateService', $serviceItem->id], 'files'=>true]) !!}
                @include('transaction.service.form')

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="btn-group pull-right hidden-xs">
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
                        <a href="{{ URL::previous() }}" class="btn btn-default">Cancel</a>
                    </div>
                    <div class="visible-xs">
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary btn-block']) !!}
                        <a href="{{ URL::previous() }}" class="btn btn-default btn-block">Cancel</a>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop