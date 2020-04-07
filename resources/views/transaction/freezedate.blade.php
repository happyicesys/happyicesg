@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="freezeDateController">

    <div class="panel-heading">
        <div class="col-md-12 col-sm-12 col-xs-12">
            Freeze Invoice Date
        </div>
    </div>

    <div class="panel-body">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {!! Form::open(['id'=>'form_submit', 'method'=>'POST','action'=>['TransactionController@freezeInvoiceDate']]) !!}
                <div class="form-group">
                    {!! Form::label('freeze_date', 'Invoice Freeze Date (Current Freezed Date: @{{form.freeze_date}})', ['class'=>'control-label']) !!}
                    <datepicker>
                        <input
                            type = "text"
                            name = "freeze_date"
                            class = "form-control"
                            placeholder = "Invoice Freeze Date"
                            ng-model = "form.freeze_date"
                            ng-change = "freezeDateChanged(form.freeze_date)"
                        />
                    </datepicker>
                </div>
            {!! Form::close() !!}

            <div class="pull-right" style="padding-top: 15px;">
                {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-success', 'form'=>'form_submit']) !!}
                <a href="/transaction" class="btn btn-default">Cancel</a>
            </div>
        </div>

    </div>
</div>
</div>
@stop

@section('footer')
<script src="/js/freeze_date.js"></script>
@stop