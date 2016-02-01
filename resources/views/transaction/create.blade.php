@inject('people', 'App\Person')

@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="transController">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New {{$TRANS_TITLE}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($transaction = new \App\Transaction, ['action'=>'TransactionController@store']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('person_id', 'Customer', ['class'=>'control-label']) !!}
                        {!! Form::select('person_id', 
                            $people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->lists('full', 'id'), 
                            null, 
                            [
                            'id'=>'person_id', 
                            'class'=>'select form-control', 
                            ]) 
                        !!}
                    </div>      
                </div> 
            </div>           

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group pull-right" style="padding: 30px 0px 0px 0px;">
                        {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                        <a href="/transaction" class="btn btn-default">Cancel</a>            
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

<script src="/js/transaction_create.js"></script>  
<script>
    $('.select').select2();
</script>

@stop