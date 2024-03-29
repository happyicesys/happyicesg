@inject('uoms', 'App\Uom')
@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="transactionController" ng-cloak>
    @php
        //state 1 = cancel inv - confirm - cancel (pending)
        //state 2 = cancel inv - del paid - del owe - update - print - send inv - cancel (confirmed)
        //state 3 = cancel inv - paid - update - print - send inv - cancel (delivered unpaid)
        //state 4 = delete inv - undo - cancel (cancelled)
        //state 5 = cancel inv - unpaid - update - print - send inv - cancel (delivered paid)
        //state 6 = LOCKED label - cancel (is freeze true)

        $state = '';
        $status = $transaction->status;
        $pay_status = $transaction->pay_status;

        if($transaction->is_freeze) {
            $state = 6;
        }else {
            switch($status) {
                case 'Pending':
                    $state = 1;
                    break;
                case 'Confirmed':
                    $state = 2;
                    break;
                case 'Delivered':
                    $state = 3;
                case 'Verified Owe':
                case 'Verified Paid':
                    if($pay_status === 'Paid') {
                        $state = 6;
                    }else {
                        $state = 4;
                    }
                    break;
                case 'Cancelled':
                    $state = 5;
                    break;
                default:
                    $state = 7;
            }
        }
    @endphp

    <div class="panel-heading">
        <div class="col-md-6 col-xs-12">
        <h4>
            @if($transaction->is_discard)
                <span class="label label-danger">Discard</span>
            @endif
            @if($transaction->is_service)
                <i class="fa fa-wrench" aria-hidden="true"></i>
                <strong>Service : {{$transaction->id}}</strong>
                ({{$transaction->status}})
            @else
                @if($transaction->status == 'Cancelled')
                    <del><strong>Invoice : {{$transaction->id}}</strong></del>
                    <br>
                    @if($transaction->cancel_reason_option)
                        <span>
                            @if($transaction->cancel_reason_option == 1)
                                (Customer cancel 客户取消单)
                            @elseif($transaction->cancel_reason_option == 2)
                                (Supervisor cancel 主管取消单)
                            @elseif($transaction->cancel_reason_option == 3)
                                (Wrong invoice/SN 开错单)
                            @elseif($transaction->cancel_reason_option == 4)
                                <p>
                                    (Other Reason: {{$transaction->cancel_reason_remarks}})
                                </p>
                            @endif
                        </span>
                    @endif
                @else
                    <strong>Invoice : {{$transaction->id}}</strong>
                    ({{$transaction->status}}) - {{$transaction->pay_status}}
                @endif
            @endif

            {!! Form::text('transaction_id', $transaction->id, ['id'=>'transaction_id','class'=>'hidden form-control']) !!}
        </h4>
        </div>
        <div class="col-md-6 col-xs-12">
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

    @php
        $disabled = false;
        $disabledStr = '';

        if(auth()->user()->hasRole('watcher') or auth()->user()->hasRole('subfranchisee') or auth()->user()->hasRole('event') or auth()->user()->hasRole('event_plus')) {
            $disabled = true;
            $disabledStr = 'disabled';
        }
    @endphp

    {!! Form::model($transaction, ['id'=>'log', 'method'=>'GET', 'action'=>['TransactionController@generateLogs', $transaction->id]]) !!}
    {!! Form::close() !!}

    {!! Form::model($transaction, ['id'=>'replicate', 'method'=>'POST', 'action'=>['TransactionController@replicateTransaction', $transaction->id]]) !!}
    {!! Form::close() !!}

    {!! Form::model($transaction, ['id'=>'new_transaction', 'method'=>'POST', 'action'=>['TransactionController@store']]) !!}
        <input type="text" class="hidden" name="person_id" value="{{$transaction->person->id}}">
    {!! Form::close() !!}

    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                <div class="pull-right btn-group">
                    @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <button type="submit" class="btn btn-success" form="new_transaction"><i class="fa fa-plus"></i> New Transaction {{$transaction->person->code}} ({{$transaction->person->custPrefix->code}})</button>
                        @if(!$transaction->is_service)
                            {{-- {!! Form::submit('Discard Item(s)', ['class'=> 'btn btn-danger', 'type'=>'button', 'name'=>'discard', 'form'=>'new_transaction']) !!} --}}
                            {{-- @if(!auth()->user()->hasRole('hd_user')) --}}
                            @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                {!! Form::submit('Replicate', ['class'=> 'btn btn-default', 'form'=>'replicate']) !!}
                            @endif
                        @endif
                        {!! Form::submit('Log History', ['class'=> 'btn btn-warning', 'form'=>'log']) !!}
                        @if(!$transaction->deals()->exists() and !$transaction->is_service and !auth()->user()->hasRole('driver'))
                            @if($state == 1 or $state == 2 or $state == 5)
                                {!! Form::submit('Convert to Service', ['name'=>'is_service', 'class'=> 'btn btn-default', 'form'=>'form_service']) !!}
                            @endif
                        @endif
                        {{-- @endif --}}
                    @endif
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    {!! Form::model($transaction,['id'=>'form_cust', 'method'=>'PATCH','action'=>['TransactionController@update', $transaction->id], 'autocomplete'=>'off']) !!}
                        @include('transaction.form_cust')
                    @if(!$transaction->is_service)
                        <div class="row">
                            <div class="col-md-12" style="padding-top:15px;">
                                @include('transaction.form_dealtable')
                            </div>
                        </div>
                        @if(!auth()->user()->hasRole('hd_user'))
                            <div class="row">
                                <div class="col-md-12" style="padding-top:15px;">
                                    @include('transaction.form_table2')
                                </div>
                            </div>
{{--
                            @unless($transaction->status == 'Delivered' and $transaction->pay_status == 'Paid')
                                <div class="row">
                                    <div class="col-md-12" style="padding-top:15px;">
                                        @include('transaction.form_table')
                                    </div>
                                </div>
                            @else
                                @cannot('transaction_view')
                                @cannot('supervisor_view')
                                <div class="row">
                                    <div class="col-md-12" style="padding-top:15px;">
                                        @include('transaction.form_table')
                                    </div>
                                </div>
                                @endcannot
                                @endcannot
                            @endunless --}}
                        @endif
                    @else
                        <div class="row" style="padding-top:15px;">
                            @include('transaction.form_service_2')
                        </div>
                    @endif
                    {!! Form::close() !!}

                    {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['TransactionController@destroy', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to cancel invoice?")']) !!}
                    {!! Form::close() !!}
                    {!! Form::open([ 'id'=>'form_reverse', 'method'=>'POST', 'action'=>['TransactionController@reverse', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to reverse the cancellation?")']) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['id'=>'form_service', 'method'=>'POST','action'=>['TransactionController@isServiceChanged', $transaction->id, 1]]) !!}
                    {!! Form::close() !!}
                </div>
            </div>

            @if($state === 1)
            <div class="row hidden-xs">
                <div class="col-md-12">
                    <div class="pull-left">
                        <div class="btn-group">
                            @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                                @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                                    <button class="btn btn-danger" data-toggle="modal" data-target="#cancelConfirmationModal">
                                        Cancel Invoice
                                    </button>
                                @else
                                    {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group">
                            @if(auth()->user()->hasRole('hd_user'))
                                {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!}
                                {!! Form::submit('Save Draft', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                            @else
                                {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!}
                            @endif
                            <a href="/transaction" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="visible-xs">
                @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                    @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                        <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelConfirmationModal">
                            Cancel Invoice
                        </button>
                    @else
                        {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger btn-block', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                    @endif
                @endif

                @if(auth()->user()->hasRole('hd_user'))
                    {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary btn-block', 'form'=>'form_cust', 'style'=>'margin-top: 50px;']) !!}
                    {!! Form::submit('Save Draft', ['name'=>'update', 'class'=> 'btn btn-default btn-block', 'form'=>'form_cust']) !!}
                @else
                    {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary btn-block', 'form'=>'form_cust', 'style'=>'margin-top: 50px;']) !!}
                @endif
                <a href="/transaction" class="btn btn-default btn-block">Back</a>
            </div>
            @elseif($state === 2)
            <div class="row hidden-xs">
                <div class="col-md-12">
                    <div class="pull-left">
                        <div class="btn-group">
                            @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                                @unless(auth()->user()->hasRole('driver') and $transaction->deals()->exists())
                                    @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                                        <button class="btn btn-danger" data-toggle="modal" data-target="#cancelConfirmationModal">
                                            Cancel Invoice
                                        </button>
                                    @else
                                        {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                                    @endif
                                @endunless
                                @if(!$transaction->is_service)
                                    @if($transaction->pay_status === 'Owe')
                                        {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success', 'form'=>'form_cust']) !!}
                                    @else
                                        @if(!auth()->user()->hasRole('driver'))
                                            {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                                        @endif
                                    @endif
                                @endif
                                {!! Form::submit('Delivered', ['name'=>'del', 'class'=> 'btn btn-warning', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!}
                            @endif
                        </div>
                    </div>

                    <div class="pull-right">
                        <div class="btn-group">
                            @if(!auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}

                                <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print Invoice</a>
                                @if(!$transaction->is_service and !auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                    <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-warning">Send Inv Email</a>
                                @endif
                            @endif
                            @if(!$transaction->is_service)
                                <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary">Print DO</a>
                                <a href="/transaction/download/{{$transaction->id}}?value=quotation" class="btn btn-warning">Quotation</a>
                            @endif
                            <a href="/transaction" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="visible-xs">
                @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                    @unless(auth()->user()->hasRole('driver') and $transaction->deals()->exists())
                        @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                            <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelConfirmationModal">
                                Cancel Invoice
                            </button>
                        @else
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger btn-block', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                        @endif
                    @endunless
                    @if(!$transaction->is_service)
                        @if($transaction->pay_status === 'Owe')
                            {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success btn-block', 'form'=>'form_cust']) !!}
                        @else
                            @if(!auth()->user()->hasRole('driver'))
                                {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning btn-block', 'form'=>'form_cust']) !!}
                            @endif
                        @endif
                    @endif
                    {!! Form::submit('Delivered', ['name'=>'del', 'class'=> 'btn btn-warning btn-block', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!}
                @endif

                @if(!auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default btn-block', 'form'=>'form_cust', 'style'=>'margin-top:50px;']) !!}
                    <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary btn-block">Print Invoice</a>
                    @if(!$transaction->is_service)
                        <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-warning btn-block">Send Inv Email</a>
                    @endif
                @endif
                @if(!$transaction->is_service)
                    <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary btn-block">Print DO</a>
                    <a href="/transaction/download/{{$transaction->id}}?value=quotation" class="btn btn-warning">Quotation</a>
                @endif
                <a href="/transaction" class="btn btn-default btn-block">Back</a>
            </div>
            @elseif($state === 3 or $state === 4)
            <div class="col-md-12 hidden-xs">
                <div class="row">
                    <div class="pull-left">
                        <div class="btn-group">
                            @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                                @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                                    <button class="btn btn-danger" data-toggle="modal" data-target="#cancelConfirmationModal">
                                        Cancel Invoice
                                    </button>
                                @else
                                    {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                                @endif

                                @if(!$transaction->is_service)
                                    @if($transaction->pay_status === 'Owe')
                                        {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success', 'form'=>'form_cust']) !!}
                                    @else
                                        {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group">
                            @if(!auth()->user()->hasRole('hd_user'))
                                {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                            @endif
                            <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print Invoice</a>
                            @if(!$transaction->is_service)
                                <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-warning">Print DO</a>
                                <a href="/transaction/download/{{$transaction->id}}?value=quotation" class="btn btn-warning">Quotation</a>
                                <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary">Send Inv Email</a>
                            @endif
                            <a href="/transaction" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="visible-xs">
                @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                    @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                        <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelConfirmationModal">
                            Cancel Invoice
                        </button>
                    @else
                        {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger btn-block', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                    @endif

                    @if(!$transaction->is_service)
                        @if($transaction->pay_status === 'Owe')
                            {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success btn-block', 'form'=>'form_cust']) !!}
                        @else
                            {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning btn-block', 'form'=>'form_cust']) !!}
                        @endif
                    @endif
                @endif

                @if(!auth()->user()->hasRole('hd_user'))
                    {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default btn-block', 'form'=>'form_cust', 'style'=>'margin-top:50px;']) !!}
                @endif
                <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary btn-block">Print Invoice</a>
                @if(!$transaction->is_service)
                    <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-warning btn-block">Print DO</a>
                    <a href="/transaction/download/{{$transaction->id}}?value=quotation" class="btn btn-warning">Quotation</a>
                    <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary btn-block">Send Inv Email</a>
                @endif
                <a href="/transaction" class="btn btn-default btn-block">Back</a>
            </div>
            @elseif($state === 5)
            <div class="col-md-12 hidden-xs">
                <div class="row">
                    <div class="pull-right">
                        <div class="btn-group">
                            @cannot('transaction_view')
                            @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_wipe']) !!}
                                {!! Form::submit('Undo Cancel', ['class'=> 'btn btn-warning', 'form'=>'form_reverse', 'name'=>'form_reverse']) !!}
                            @endif
                            @endcan
                            <a href="/transaction" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="visible-xs">
                @cannot('transaction_view')
                @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                    {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger btn-block', 'form'=>'form_delete', 'name'=>'form_wipe']) !!}
                    {!! Form::submit('Undo Cancel', ['class'=> 'btn btn-warning btn-block', 'form'=>'form_reverse', 'name'=>'form_reverse']) !!}
                @endif
                @endcan
                <a href="/transaction" class="btn btn-default btn-block">Back</a>
            </div>
            @elseif($state === 6)
            <div class="col-md-12 hidden-xs">
                <div class="row">
                    <div class="pull-left">
                        <div class="btn-group">
                            @cannot('transaction_view')
                            @cannot('supervisor_view')
                            @if(!auth()->user()->hasRole('franchisee')and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                                @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                                    <button class="btn btn-danger" data-toggle="modal" data-target="#cancelConfirmationModal">
                                        Cancel Invoice
                                    </button>
                                @else
                                    {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                                @endif
                                {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                            @endif
                            @endcannot
                            @endcannot
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group">
                            @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                            @endif
                            <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary">Send Inv Email</a>
                            <a href="/transaction/download/{{$transaction->id}}" class="btn btn-default">Print Invoice</a>
                            {{-- @if($transaction->is_deliveryorder) --}}
                                <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary">Print DO</a>
                                <a href="/transaction/download/{{$transaction->id}}?value=quotation" class="btn btn-warning">Quotation</a>
                            {{-- @endif --}}
                            <a href="/transaction" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="visible-xs">
                    @cannot('transaction_view')
                    @cannot('supervisor_view')
                    @if(!auth()->user()->hasRole('franchisee')and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus') and !auth()->user()->hasRole('salesperson'))
                        @if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                            <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelConfirmationModal">
                                Cancel Invoice
                            </button>
                        @else
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger  btn-block', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                        @endif
                        {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning  btn-block', 'form'=>'form_cust']) !!}
                    @endif
                    @endcannot
                    @endcannot

                    @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                    {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-warning btn-block', 'form'=>'form_cust', 'style' => 'margin-top: 50px;']) !!}
                    @endif
                    <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary btn-block">Send Inv Email</a>
                    <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary btn-block">Print Invoice</a>
                        <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary btn-block">Print DO</a>
                        <a href="/transaction/download/{{$transaction->id}}?value=quotation" class="btn btn-warning">Quotation</a>
                    <a href="/transaction" class="btn btn-default btn-block">Back</a>
            </div>
            @elseif($state === 7)
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="pull-right">
                        <span class="bg-info" style="margin-right: 15px;">
                            <i class="fa fa-lock"></i>
                            This invoice has been locked
                        </span>
                        <a href="/transaction" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
            @endif
    </div>

    @if(!$transaction->is_service)
    <div class="panel-footer">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Printable Attachment(s)
            </div>
            <div class="panel-body">
                {!! Form::open(['action'=>['TransactionController@updateFilesName', $transaction->id]]) !!}
                <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-5 text-center">
                            Image
                        </th>
                        <th class="col-md-5 text-center">
                            Name
                        </th>
                        <th class="col-md-1 text-center">
                            Action
                        </th>
                    </tr>

                    <tbody>
                        @unless(count($invattachments)>0)
                            <td class="text-center" colspan="12">No Records Found</td>
                        @else
                            @foreach($invattachments as $index => $invattachment)

                            @php
                                $ext = pathinfo($invattachment->path, PATHINFO_EXTENSION);
                            @endphp

                            <tr>
                                <td class="col-md-1 text-center">
                                    {{ $index + 1 }}
                                </td>
                                <td class="col-md-5">
                                    @if($ext == 'pdf')
                                        <embed src="{{$invattachment->path}}" type="application/pdf" style="max-width:350px; max-height:500px;">
                                    @else
                                        <a href="{{$invattachment->path}}">
                                            <img src="{{$invattachment->path}}" alt="{{$invattachment->name}}" style="max-width:350px; max-height:350px;">
                                        </a>
                                    @endif
                                </td>
                                <td class="col-md-5">
                                    {{-- <input type="text" class="form-control" name="file_name[{{$file->id}}]" value="{{$file->name}}" style="min-width: 300px;"> --}}
                                        <textarea class="form-control" name="name[{{$invattachment->id}}]" rows="5" style="min-width: 300px;">{{$invattachment->name}}</textarea>
                                </td>
                                <td class="col-md-1 text-center">
                                    @if(!auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                        <button type="submit" form="remove_file" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> <span class="hidden-xs">Delete</span></button>
                                        @if($ext == 'pdf')
                                            <a href="{{$invattachment->path}}" class="btn btn-sm btn-info">Download</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endunless
                        <tr>
                            <td colspan="4">
                                <button type="submit" class="btn btn-success pull-right"><i class="fa fa-check"></i> <span class="hidden-xs">Save Files Name</span></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
                {!! Form::close() !!}

                @if(count($invattachments) > 0)
                    {!! Form::open(['id'=>'remove_file', 'method'=>'DELETE', 'action'=>['TransactionController@removeAttachment', $invattachment->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                    {!! Form::close() !!}
                @endif

                @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') or (auth()->user()->hasRole('hd_user') and $transaction->status == 'Pending') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                {!! Form::open(['action'=>['TransactionController@addInvoiceAttachment', $transaction->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
                @endif
{{--                 <form action="/transaction/invoice/attach" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="file" name="img_file[]" multiple>
                    <button type="submit" class="pull-right btn btn-success">Upload</button>
                </form> --}}
                {!! Form::close() !!}
                <label class="pull-right totalnum" for="totalnum">
                    Total of {{count($invattachments)}} entries
                </label>
            </div>
        </div>

        @if($transaction->is_deliveryorder and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Signature &nbsp;
                    <span ng-if="form.sign_url">
                        <button type="button" ng-click="deleteSignature()" class="btn btn-danger btn-md">
                            Delete Existing
                        </button>
                    </span>
                    <span ng-if="!form.sign_url">
                        <button class="btn btn-md btn-default" ng-click="onSignatureCaretClicked()">
                            <i class="fa fa-caret-down" ng-if="hideSignature"></i>
                            <i class="fa fa-caret-right" ng-if="!hideSignature"></i>
                        </button>
                        <button type="button" ng-click="clearSignature()" class="btn btn-default btn-md">
                            Clear
                        </button>
                        <button type="button" ng-click="submitSignature()" class="btn btn-success btn-md">
                            Submit
                        </button>
                    </span>
                </div>
                <img ng-src="@{{form.sign_url}}" style="max-width:500px; max-height:500px;" ng-show="form.sign_url">
                <div class="panel-body" ng-show="!hideSignature && !form.sign_url">
                    @if(!auth()->user()->hasRole('watcher'))
                        <canvas id="canvas" name="signature" width=1000 height=450 ></canvas>
                    @endif
                </div>
            </div>
        @endif
    </div>
    @endif

    <div id="cancelConfirmationModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Please choose a cancel reason</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancel_reason_option">
                            Reason to Cancel
                        </label>
                        <label for="*" style="color: red;">*</label>
                        <select name="cancel_reason_option" class="select form-control" ng-model="cancelForm.cancel_reason_option">
                            <option value="">Select...</option>
                            <option value="1">
                                Customer cancel 客户取消单
                            </option>
                            <option value="2">
                                Supervisor cancel 主管取消单
                            </option>
                            <option value="3">
                                Wrong invoice/SN 开错单
                            </option>
                            <option value="4">
                                Others 其它原因
                            </option>
                        </select>
                    </div>
                    <div class="form-group" ng-if="cancelForm.cancel_reason_option == 4">
                        <label for="cancel_reason_remarks">
                            Please state your reason
                        </label>
                        <label for="*" style="color: red;">*</label>
                        <textarea name="cancel_reason_remarks" class="form-control" ng-model="cancelForm.cancel_reason_remarks" rows="5"></textarea>
                    </div>
                    <div class="form-group">

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button class="btn btn-success pull-right" ng-click="onCancelConfirmationClicked($event)" ng-disabled="!cancelForm.cancel_reason_option || (cancelForm.cancel_reason_option == 4 && !cancelForm.cancel_reason_remarks)">
                            Submit
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
@stop

@section('footer')
<script src="/js/transaction.js"></script>
<script>
    function clicked(e){
        if(!confirm('Are you sure?'))e.preventDefault();
    }
    $('.select').select2({
        placeholder: 'Please Select'
    });

    $('.datepick').datetimepicker({
       format: 'YYYY-MM-DD'
    });

    $(document).ready(function() {
        Dropzone.autoDiscover = false;
        $('.dropzone').dropzone({
            init: function()
            {
                this.on("complete", function()
                {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    location.reload();
                  }
                });
            }

        });
    });
</script>
@stop