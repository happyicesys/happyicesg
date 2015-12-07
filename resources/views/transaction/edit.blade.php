@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="transactionController">

    <div class="panel-heading">
        <div class="col-md-6">
        <h4>
            <strong>Invoice : {{$transaction->id}}</strong> ({{$transaction->status}}) - {{$transaction->pay_status}}
            {!! Form::text('transaction_id', $transaction->id, ['id'=>'transaction_id','class'=>'hidden form-control']) !!}
        </h4>
        </div>
        <div class="col-md-6">
        <label class="pull-right" style="padding-top: 10px">Created by : {{ $transaction->user->name }}</label>
        </div>
    </div>

    <div class="panel-body">

    <div class="col-md-12">
        <div class="col-md-6">
            {!! Form::model($transaction,['id'=>'form_cust', 'method'=>'PATCH','action'=>['TransactionController@update', $transaction->id]]) !!}            
                @include('transaction.form_cust')   
            {!! Form::close() !!}    

            {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['TransactionController@destroy', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
            {!! Form::close() !!}
        </div>              

        <div>
        <div class="col-md-6">    
            {!! Form::model($deal = new \App\Deal, ['id'=>'form_item', 'action'=>['DealController@store']]) !!}
                @include('transaction.form_item')
            {!! Form::close() !!}   
        </div>

            @if($transaction->status == 'Pending' and $transaction->pay_status == 'Owe')
            <div class="col-md-6" style="padding-top:125px;">
                <div class="pull-left">
                    {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'form_delete']) !!}
                </div>
                <div class="pull-right">

                    {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!} 
                    {!! Form::submit('Save', ['name'=>'save', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!} 
                    <a href="/transaction" class="btn btn-default">Cancel</a>   
                </div>        
            </div> 
            @elseif($transaction->status == 'Confirmed' and $transaction->pay_status == 'Owe')
            <div class="col-md-6" style="padding-top:80px;">
                <div class="pull-left">
                    {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'disabled'=>'disabled']) !!}
                </div>
                <div class="pull-right">
                
                    {!! Form::open(['id'=>'form_print', 'method'=>'POST', 'action'=>['TransactionController@generateInvoice', $transaction->id]]) !!}
                    {!! Form::close() !!}

                    {!! Form::submit('Payment Confirmed', ['name'=>'pay', 'class'=> 'btn btn-success', 'form'=>'form_cust']) !!} 
                    {!! Form::submit('Print', ['class'=> 'btn btn-primary', 'form'=>'form_print']) !!}
                    {!! Form::submit('Update', ['name'=>'confirm', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!} 
                    <a href="/transaction" class="btn btn-default">Cancel</a>   
                </div>        
            </div>
            @else 
            <div class="col-md-6" style="padding-top:80px;">
                <div class="pull-left">
                    {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'disabled'=>'disabled']) !!}
                </div>
                <div class="pull-right">
                
                    {!! Form::open(['id'=>'form_print', 'method'=>'POST', 'action'=>['TransactionController@generateInvoice', $transaction->id]]) !!}
                    {!! Form::close() !!}

                    {!! Form::submit('Print', ['class'=> 'btn btn-primary', 'form'=>'form_print']) !!}
                    {!! Form::submit('Update', ['name'=>'confirm', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!} 
                    <a href="/transaction" class="btn btn-default">Cancel</a>   
                </div>        
            </div>                        
            @endif

        </div> 
    </div>      


        <div class="col-md-12">
            @include('transaction.form_table')
        </div>


    </div>
</div>
</div>
@stop

@section('footer')
<script src="/js/transaction.js"></script>  
@stop