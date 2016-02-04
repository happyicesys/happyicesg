@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="transactionController">

    <div class="panel-heading">
        <div class="col-md-4">
        <h4>
            @if($transaction->status == 'Cancelled')
            <del><strong>Invoice : {{$transaction->id}}</strong> ({{$transaction->status}}) - {{$transaction->pay_status}}</del>
            @else
            <strong>Invoice : {{$transaction->id}}</strong> ({{$transaction->status}}) - {{$transaction->pay_status}}
            @endif
            {!! Form::text('transaction_id', $transaction->id, ['id'=>'transaction_id','class'=>'hidden form-control']) !!}
        </h4>
        </div>
        <div class="col-md-8">
            @if($transaction->paid_by)
            <div class="col-md-4">
                <label style="padding-top: 10px" class="pull-right">Paid by : {{ $transaction->paid_by }}</label>
            </div>
            @else
            <div class="col-md-4"></div>
            @endif        
            @if($transaction->driver)
            <div class="col-md-4">
                <label style="padding-top: 10px" class="pull-right">Delivered by : {{ $transaction->driver }}</label>
            </div>
            @else
            <div class="col-md-4"></div>
            @endif
            <div class="col-md-4">
                <label style="padding-top: 10px" class="pull-right">Last Modified: {{ $transaction->updated_by }}</label>
            </div>
        </div>
    </div>

    <div class="panel-body">
        
            {!! Form::model($transaction, ['id'=>'log', 'method'=>'POST', 'action'=>['TransactionController@generateLogs', $transaction->id]]) !!}
            {!! Form::close() !!}
        <div class="row">
            <div class="col-md-12" style="padding: 0px 30px 10px 0px;">    
                {!! Form::submit('Log History', ['class'=> 'btn btn-warning pull-right', 'form'=>'log']) !!}            
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::model($transaction,['id'=>'form_cust', 'method'=>'PATCH','action'=>['TransactionController@update', $transaction->id]]) !!}            
                        @include('transaction.form_cust') 
                    
                    <div class="row">
                        <div class="col-md-12" style="padding-top:15px;">
                            @include('transaction.form_dealtable')
                        </div>           
                    </div>  
                   
                    @unless($transaction->status == 'Delivered' and $transaction->pay_status == 'Paid')
                        <div class="row">
                            <div class="col-md-12" style="padding-top:15px;">
                                @include('transaction.form_table')
                            </div>           
                        </div> 
                    @else
                        @cannot('transaction_view')
                        <div class="row">
                            <div class="col-md-12" style="padding-top:15px;">
                                @include('transaction.form_table')
                            </div>           
                        </div>
                        @endcannot                     
                    @endunless              
                    {!! Form::close() !!}    

                    {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['TransactionController@destroy', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to cancel invoice?")']) !!}                
                    {!! Form::close() !!}
                </div> 
            </div>             

                @if($transaction->status == 'Pending' and $transaction->pay_status == 'Owe')
                <div class="row">
                    <div class="col-md-12" >
                        <div class="pull-left">
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                        </div>
                        <div class="pull-right">

                            {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!} 
                            {!! Form::submit('Save', ['name'=>'save', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!} 
                            <a href="/transaction" class="btn btn-default">Cancel</a>   
                        </div>        
                    </div>
                </div> 
                @elseif($transaction->status == 'Confirmed' and $transaction->pay_status == 'Owe')
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-left">
                            @can('transaction_deleteitem')
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @endcan
                        </div>
                        <div class="pull-right">

                            {!! Form::submit('Delivered & Paid', ['name'=>'del_paid', 'class'=> 'btn btn-success', 'form'=>'form_cust']) !!}
                            {!! Form::submit('Delivered & Owe', ['name'=>'del_owe', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                            {!! Form::submit('Update', ['name'=>'confirm', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}  
                            <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print</a>
                            <a href="/transaction" class="btn btn-default">Cancel</a>
                               
                        </div>        
                    </div>
                </div>
                @elseif(($transaction->status == 'Delivered' or $transaction->status == 'Verified Owe') and $transaction->pay_status == 'Owe') 
                <div class="col-md-12">
                    <div class="row">
                        <div class="pull-left">
                            @can('transaction_deleteitem')
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @endcan
                        </div>
                        <div class="pull-right">

                            {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success', 'form'=>'form_cust']) !!}
                            <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print</a>
                            {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!} 
                            <a href="/transaction" class="btn btn-default">Cancel</a>   

                        </div> 
                    </div>       
                </div>
                @elseif($transaction->status == 'Cancelled') 
                <div class="col-md-12">
                    <div class="row">
                        <div class="pull-right">
                            <a href="/transaction" class="btn btn-default">Cancel</a>
                            @cannot('transaction_view')
                                {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_wipe']) !!}
                            @endcan                               
                        </div> 
                    </div>       
                </div>                
                @else
                <div class="col-md-12">
                    <div class="row">
                        <div class="pull-left">
                            @can('transaction_deleteitem')
                            @cannot('transaction_view')
                                {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @endcan
                            @endcan
                        </div>
                        <div class="pull-right">
                            @cannot('transaction_view')
                                {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                            @endcan                        
                            <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print</a>
                            <a href="/transaction" class="btn btn-default">Cancel</a>   
                        </div> 
                    </div>       
                </div>                                        
                @endif

        </div>      

    </div>
</div>
</div>
@stop

@section('footer')
<script src="/js/transaction.js"></script>  
@stop