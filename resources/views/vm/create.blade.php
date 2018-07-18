@extends('template')
@section('title')
Vending Machine
@stop
@section('content')

<div class="create_edit">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>New Vending Machine</strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($vending = new \App\Vending, ['action'=>'VMController@storeVending']) !!}

                @include('vm.form')

                <div class="btn-group pull-right">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                    {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                    <a href="/vm" class="btn btn-default">Cancel</a>
                    </div>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@stop