@extends('template')
@section('title')
Service
@stop
@section('content')

<div class="create_edit">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>New Service Job for {{$transaction->id}} <br> ({{$transaction->person->cust_id}} - {{$transaction->person->company}})</strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($serviceItem = new \App\ServiceItem, ['action'=>'TransactionController@storeService']) !!}

                @include('transaction.service.form')

                <div class="col-md-12 hidden-xs">
                    <div class="btn-group pull-right">
                        {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                        <a href="{{ URL::previous() }}" class="btn btn-default">Back</a>
                    </div>
                </div>
                <div class="visible-xs">
                    <div class="col-xs-12">
                        {!! Form::submit('Add', ['class'=> 'btn btn-success btn-block']) !!}
                        <a href="{{ URL::previous() }}" class="btn btn-default btn-block">Back</a>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@stop