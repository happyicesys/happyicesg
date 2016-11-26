@inject('people', 'App\Person')
@extends('template')
@section('title')
Deals
@stop
@section('content')
<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="dealsController">
    <div class="panel-heading">
        <div class="col-md-4">
        <h4>
            @if($transaction->status === 'Cancelled' or $transaction->status === 'Deleted')
            <del><strong>Invoice : {{$transaction->transaction_id ? $transaction->transaction_id : $transaction->id}}</strong> ({{$transaction->status}})
                @unless($transaction->person->cust_id[0] == 'D' or $transaction->person->cust_id[0] == 'H')
                    - {{$transaction->pay_status}}</del>
                @endunless
            @else
            <strong>Invoice : {{$transaction->transaction_id ? $transaction->transaction_id : $transaction->id}}</strong> ({{$transaction->status}})
                @unless($transaction->person->cust_id[0] ==='D' or $transaction->person->cust_id[0] === 'H')
                    - {{$transaction->pay_status}}
                @endunless
            @endif
            {!! Form::hidden('transaction_id', $transaction->id, ['id'=>'transaction_id','class'=>'form-control']) !!}
        </h4>
        </div>
    </div>
    <div class="panel-body">
            {!! Form::model($transaction, ['id'=>'log', 'method'=>'POST', 'action'=>['MarketingController@generateLogs', $transaction->id]]) !!}
            {!! Form::close() !!}
        <div class="row">
            <div class="col-md-12" style="padding: 0px 30px 10px 0px;">
                {!! Form::submit('Log History', ['class'=> 'btn btn-warning pull-right', 'form'=>'log']) !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::model($transaction,['id'=>'form_cust', 'method'=>'PATCH','action'=>['MarketingController@update', $transaction->id]]) !!}
                        @include('market.deal.form_cust')
                    <div class="row">
                        <div class="col-md-12" style="padding-top:15px;">
                            @include('transaction.form_dealtable')
                        </div>
                    </div>
                    @unless($transaction->status == 'Delivered' and $transaction->pay_status == 'Paid')
                        <div class="row">
                            <div class="col-md-12" style="padding-top:15px;">
                                @include('market.deal.form_table')
                            </div>
                        </div>
                    @else
                        @cannot('transaction_view')
                        @cannot('supervisor_view')
                        <div class="row">
                            <div class="col-md-12" style="padding-top:15px;">
                                @include('market.deal.form_table')
                            </div>
                        </div>
                        @endcannot
                        @endcannot
                    @endunless
                    {!! Form::close() !!}
                    {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['MarketingController@destroy', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to cancel invoice?")']) !!}
                    {!! Form::close() !!}
                    {!! Form::open([ 'id'=>'form_reverse', 'method'=>'POST', 'action'=>['MarketingController@reverse', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to reverse the cancellation?")']) !!}
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
                            @unless($transaction->person->cust_id[0] == 'D')
                                {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!}
                            @else
                                {!! Form::submit('Confirm', ['name'=>'submit_deal', 'class'=> 'btn btn-success', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!}
                            @endunless
                            <a href="/market/deal" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </div>
                @elseif(($transaction->status == 'Confirmed' and $transaction->pay_status == 'Owe') or ($transaction->status == 'Draft' and $transaction->pay_status == 'Owe'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-left">
                            {{-- original with 1 day prior disable setting --}}
                            {{-- @unless($transaction->person->cust_id[0] === 'D' and $people::where('user_id', Auth::user()->id)->first() ? $people::where('user_id', Auth::user()->id)->first()->cust_type === 'AB' : false and $transaction->status === 'Confirmed' and \Carbon\Carbon::today() >= \Carbon\Carbon::parse($transaction->delivery_date)->subDay()) --}}
                            @unless($noneditable)
                                {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @else
                                {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete', 'disabled'=>'disabled']) !!}
                            @endunless
                        </div>
                        <div class="pull-right">
                                @unless($noneditable)
                                    {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                                @else
                                    {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust', 'disabled'=>'disabled']) !!}
                                @endunless
                            <a href="/market/deal/emailInv/{{$transaction->id}}" class="btn btn-warning">Send Inv Email</a>
                            <a href="/market/deal/download/{{$transaction->id}}" class="btn btn-primary">Print</a>
                            <a href="/market/deal" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </div>

                @elseif(((($transaction->person->cust_id[0] === 'D' or $transaction->person->cust_id[0] === 'H') and $transaction->status == 'Delivered') or $transaction->status == 'Verified Owe' or $transaction->status == 'Verified Paid') and $transaction->pay_status == 'Owe')
                <div class="col-md-12">
                    <div class="row">
                        <div class="pull-left">
                            @can('transaction_deleteitem')
                            @cannot('supervisor_view')
                            @unless($transaction->person->cust_id[0] === 'D' and $transaction->status === 'Delivered')
                                {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @endunless
                            @endcannot
                            @endcan
                        </div>
                        <div class="pull-right">
                            @unless($transaction->person->cust_id[0] === 'D' and $transaction->status === 'Delivered')
                                {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!}
                            @endunless
                            <a href="/market/deal/emailInv/{{$transaction->id}}" class="btn btn-warning">Send Inv Email</a>
                            <a href="/market/deal/download/{{$transaction->id}}" class="btn btn-primary">Print</a>
                            @unless($transaction->person->cust_id[0] === 'D' and $transaction->status == 'Delivered')
                                {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                            @endunless
                            <a href="/market/deal" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </div>
                @elseif($transaction->status == 'Cancelled' or $transaction->status == 'Deleted')
                <div class="col-md-12">
                    <div class="row">
                        @if(Auth::user()->hasRole('admin'))
                            {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_wipe']) !!}
                        @endif
                        <div class="pull-right">
                            <a href="/market/deal" class="btn btn-default">Cancel</a>
                            @cannot('transaction_view')
                                {{-- {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_wipe']) !!} --}}
{{--                                 @unless($transaction->person->cust_id[0] == 'D' and $transaction->status == 'Confirmed' and \Carbon\Carbon::today() >= \Carbon\Carbon::parse($transaction->delivery_date)->subDay())
                                    {!! Form::submit('Undo Cancel', ['class'=> 'btn btn-warning', 'form'=>'form_reverse', 'name'=>'form_reverse']) !!}
                                @else
                                    {!! Form::submit('Undo Cancel', ['class'=> 'btn btn-warning', 'form'=>'form_reverse', 'name'=>'form_reverse', 'disabled'=>'disabled']) !!}
                                @endunless --}}
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
                            @cannot('supervisor_view')
                                @if($transaction->status != 'Delivered' or $transaction->status != 'Verified Owe' or $transaction->status != 'Verified Paid')
                                    {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                                @endif
                                {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                            @endcannot
                            @endcannot
                            @endcan
                        </div>
                        <div class="pull-right">
                            @cannot('supervisor_view')
                            @cannot('transaction_view')
                                {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                            @endcannot
                            @endcannot
                            <a href="/market/deal/emailInv/{{$transaction->id}}" class="btn btn-warning">Send Inv Email</a>
                            <a href="/market/deal/download/{{$transaction->id}}" class="btn btn-primary">Print</a>
                            <a href="/market/deal" class="btn btn-default">Cancel</a>
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
    <script src="/js/deal_edit.js"></script>
    <script>
        function clicked(e){
            if(!confirm('Are you sure?'))e.preventDefault();
        }
        $('.select').select2({
            placeholder: 'Please Select'
        });
    </script>
@stop