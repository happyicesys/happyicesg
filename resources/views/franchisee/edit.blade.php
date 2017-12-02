@extends('template')
@section('title')
{{ $FRANCHISE_TRANS }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="ftransactionController">

    <div class="panel-heading">
        <div class="col-md-4">
        <h4>
            @if($ftransaction->status == 'Cancelled')
            <del><strong>Invoice : {{$ftransaction->ftransaction_id}}</strong> ({{$ftransaction->status}}) - {{$ftransaction->pay_status}}</del>
            @else
            <strong>Invoice : {{$ftransaction->ftransaction_id}}</strong> ({{$ftransaction->status}}) - {{$ftransaction->pay_status}}
            @endif
            {!! Form::text('ftransaction_id', $ftransaction->id, ['id'=>'ftransaction_id','class'=>'hidden form-control']) !!}
        </h4>
        </div>
        <div class="col-md-8">
            @if($ftransaction->paid_by)
            <div class="col-md-4">
                <label style="padding-top: 10px" class="pull-right">Paid by : {{ $ftransaction->paid_by }}</label>
            </div>
            @else
            <div class="col-md-4"></div>
            @endif
            @if($ftransaction->driver)
            <div class="col-md-4">
                <label style="padding-top: 10px" class="pull-right">Delivered by : {{ $ftransaction->driver }}</label>
            </div>
            @else
            <div class="col-md-4"></div>
            @endif
            <div class="col-md-4">
                <label style="padding-top: 10px" class="pull-right">Last Modified: {{ $ftransaction->updated_by }}</label>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                {!! Form::model($ftransaction, ['id'=>'form_cust', 'method'=>'PATCH','action'=>['FtransactionController@update', $ftransaction->id]]) !!}
                    @include('franchisee.form_cust')

                <div class="row">
                    <div class="col-md-12" style="padding-top:15px;">
                        @include('franchisee.form_dealtable')
                    </div>
                </div>

                @unless($ftransaction->status == 'Delivered' and $ftransaction->pay_status == 'Paid')
                    <div class="row">
                        <div class="col-md-12" style="padding-top:15px;">
                            @include('franchisee.form_table')
                        </div>
                    </div>
                @else
                    @cannot('transaction_view')
                    @cannot('supervisor_view')
                    <div class="row">
                        <div class="col-md-12" style="padding-top:15px;">
                            @include('franchisee.form_table')
                        </div>
                    </div>
                    @endcannot
                    @endcannot
                @endunless
                {!! Form::close() !!}

                {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['FtransactionController@destroy', $ftransaction->id], 'onsubmit'=>'return confirm("Are you sure you want to cancel invoice?")']) !!}
                {!! Form::close() !!}
                {!! Form::open([ 'id'=>'form_reverse', 'method'=>'POST', 'action'=>['FtransactionController@reverse', $ftransaction->id], 'onsubmit'=>'return confirm("Are you sure you want to reverse the cancellation?")']) !!}
                {!! Form::close() !!}
            </div>
        </div>

            @php
                //state 1 = cancel inv - confirm - cancel (pending)
                //state 2 = cancel inv - del paid - del owe - update - print - send inv - cancel (confirmed)
                //state 3 = cancel inv - paid - update - print - send inv - cancel (delivered unpaid)
                //state 4 = delete inv - undo - cancel (cancelled)
                //state 5 = cancel inv - unpaid - update - print - send inv - cancel (delivered paid)
                //state 6 = LOCKED label - cancel (is freeze true)

                $state = '';
                $status = $ftransaction->status;
                $pay_status = $ftransaction->pay_status;

                if($ftransaction->is_freeze) {
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
                        case 'Verified Owe':
                        case 'Verified Paid':
                            if($pay_status === 'Paid') {
                                $state = 5;
                            }else {
                                $state = 3;
                            }
                            break;
                        case 'Cancelled':
                            $state = 4;
                            break;
                        default:
                            $state = 6;
                    }
                }
            @endphp

            @if($state === 1)
            <div class="row">
                <div class="col-md-12" >
                    <div class="pull-left">
                        {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                    </div>
                    <div class="pull-right">

                        {!! Form::submit('Confirm', ['name'=>'submit_btn', 'value'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!}
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
            @elseif($state === 2)
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left">
                        {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'submit_btn', 'value'=>'form_delete']) !!}
                    </div>

                    <div class="pull-right">
                        {!! Form::submit('Delivered & Paid', ['name'=>'submit_btn', 'value'=>'del_paid', 'class'=> 'btn btn-success', 'form'=>'form_cust', 'onclick'=>'clicked(event)' ]) !!}
                        {!! Form::submit('Delivered & Owe', ['name'=>'submit_btn', 'value'=>'del_owe', 'class'=> 'btn btn-warning', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!}
                        {!! Form::submit('Update', ['name'=>'submit_btn', 'value'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                        <a href="/franchisee/download/{{$ftransaction->id}}" class="btn btn-primary">Print</a>
                        <a href="/franchisee/emailInv/{{$ftransaction->id}}" class="btn btn-primary">Send Inv Email</a>
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
            @elseif($state === 3)
            <div class="col-md-12">
                <div class="row">
                    <div class="pull-left">
                        @can('transaction_deleteitem')
                        @cannot('supervisor_view')
                        {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'submit_btn', 'value'=>'form_delete']) !!}
                        @endcannot
                        @endcan
                    </div>
                    <div class="pull-right">
                        {!! Form::submit('Paid', ['name'=>'submit_btn', 'value'=>'paid', 'class'=> 'btn btn-success', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!}
                        {!! Form::submit('Update', ['name'=>'submit_btn', 'value'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                        <a href="/franchisee/download/{{$ftransaction->id}}" class="btn btn-primary">Print</a>
                        <a href="/franchisee/emailInv/{{$ftransaction->id}}" class="btn btn-primary">Send Inv Email</a>
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
            @elseif($state === 4)
            <div class="col-md-12">
                <div class="row">
                    <div class="pull-right">
                        @cannot('transaction_view')
                            {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'submit_btn', 'value'=>'form_wipe']) !!}
                            {!! Form::submit('Undo Cancel', ['class'=> 'btn btn-warning', 'form'=>'form_reverse', 'name'=>'submit_btn', 'value'=>'form_reverse']) !!}
                        @endcan
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
            @elseif($state === 5)
            <div class="col-md-12">
                <div class="row">
                    <div class="pull-left">
                        {{-- @can('transaction_deleteitem') --}}
                        @cannot('transaction_view')
                        @cannot('supervisor_view')
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'submit_btn', 'value'=>'form_delete']) !!}
                            {!! Form::submit('Unpaid', ['name'=>'submit_btn', 'value'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                        @endcannot
                        @endcannot
                        {{-- @endcan --}}
                    </div>
                    <div class="pull-right">
                        {!! Form::submit('Update', ['name'=>'submit_btn', 'value'=>'update', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                        <a href="/franchisee/emailInv/{{$ftransaction->id}}" class="btn btn-primary">Send Inv Email</a>
                        <a href="/franchisee/download/{{$ftransaction->id}}" class="btn btn-primary">Print</a>
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
            @elseif($state === 6)
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="pull-right">
                        <span class="bg-info" style="margin-right: 15px;">
                            <i class="fa fa-lock"></i>
                            This invoice has been locked
                        </span>
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
            @endif
    </div>
</div>
</div>
@stop

@section('footer')
<script src="/js/franchisee_edit.js"></script>
<script>
    function clicked(e){
        if(!confirm('Are you sure?'))e.preventDefault();
    }
    $('.select').select2({
        placeholder: 'Please Select'
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