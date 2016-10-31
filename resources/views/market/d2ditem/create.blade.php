@extends('template')
@section('title')
    Setup
@stop
@section('content')

<div class="create_edit">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>Add Dtd Online Items</strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($salesitem = new \App\D2dOnlineSale, ['action'=>'MarketingController@storeDtdOnlineItem']) !!}

                @include('market.d2ditem.form')

                <div class="col-md-12">
                    <div class="form-group pull-right" style="padding: 20px 95px 0px 0px">
                        {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                        <a href="/market/setup" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@stop