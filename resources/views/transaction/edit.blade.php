@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary" ng-app="app" ng-controller="transactionController" ng-cloak>

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
                    <del><strong>Invoice : {{$transaction->id}}</strong>
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
                <div class="pull-right">
                    @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        <button type="submit" class="btn btn-success" form="new_transaction"><i class="fa fa-plus"></i> New Transaction - {{$transaction->person->cust_id}}</button>
                        @if(!$transaction->is_service)
                            {!! Form::submit('Discard Item(s)', ['class'=> 'btn btn-danger', 'type'=>'button', 'name'=>'discard', 'form'=>'new_transaction']) !!}
                            {{-- @if(!auth()->user()->hasRole('hd_user')) --}}
                            @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                {!! Form::submit('Replicate', ['class'=> 'btn btn-default', 'form'=>'replicate']) !!}
                            @endif
                        @endif
                        {!! Form::submit('Log History', ['class'=> 'btn btn-warning', 'form'=>'log']) !!}
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
                        @endunless
                    @endif
                    @endif
                    {!! Form::close() !!}

                    {!! Form::open([ 'id'=>'form_delete', 'method'=>'DELETE', 'action'=>['TransactionController@destroy', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to cancel invoice?")']) !!}
                    {!! Form::close() !!}
                    {!! Form::open([ 'id'=>'form_reverse', 'method'=>'POST', 'action'=>['TransactionController@reverse', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to reverse the cancellation?")']) !!}
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

            @if($state === 1)
            <div class="row">
                <div class="col-md-12" >
                    <div class="pull-left">
                        @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @if(!$transaction->deals()->exists() and !$transaction->is_service)
                                {!! Form::submit('Convert to Service', ['name'=>'is_service', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                            @endif
                        @endif
                    </div>
                    <div class="pull-right">
                        @if(auth()->user()->hasRole('hd_user'))
                            {{-- {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust', 'ng-disabled'=>'alldata.length == 0']) !!} --}}
                            {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!}
                            {!! Form::submit('Save Draft', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                        @else
                            {!! Form::submit('Confirm', ['name'=>'confirm', 'class'=> 'btn btn-primary', 'form'=>'form_cust']) !!}
                        @endif
                        <a href="/transaction" class="btn btn-default">Back</a>

                    </div>
                </div>
            </div>
            @elseif($state === 2)
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left btn-toolbar">
                        @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            @unless(auth()->user()->hasRole('driver') and $transaction->deals()->exists())
                                {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                                @if(!$transaction->is_service and !$transaction->deals()->exists())
                                    {!! Form::submit('Convert to Service', ['name'=>'is_service', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
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

                    <div class="pull-right btn-toolbar">
                        @if(!auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        @if(!auth()->user()->hasRole('franchisee'))
{{--
                            {!! Form::submit('Delivered & Paid', ['name'=>'del_paid', 'class'=> 'btn btn-success', 'form'=>'form_cust', 'onclick'=>'clicked(event)' ]) !!}
                            {!! Form::submit('Delivered & Owe', ['name'=>'del_owe', 'class'=> 'btn btn-warning', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!} --}}
                            {{-- {!! Form::submit('Delivered', ['name'=>'del', 'class'=> 'btn btn-warning', 'form'=>'form_cust', 'onclick'=>'clicked(event)']) !!} --}}
                        @endif
                        {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                            <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print Invoice</a>
                            @if(!$transaction->is_service)
                                <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary">Send Inv Email</a>
                            @endif
                        @endif
                        @if(!$transaction->is_service)
                            <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary">Print DO</a>
                        @endif
                        <a href="/transaction" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
            @elseif($state === 3 or $state === 4)
            <div class="col-md-12">
                <div class="row">
                    <div class="pull-left">
                        @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            @if(!$transaction->is_service)
                                @if($transaction->pay_status === 'Owe')
                                    {!! Form::submit('Paid', ['name'=>'paid', 'class'=> 'btn btn-success', 'form'=>'form_cust']) !!}
                                @else
                                    {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="pull-right">

{{--                      disabled because complication, inventory changes
                        @if($state === 3 and auth()->user()->hasRole('admin')) {
                            <a href="/transaction/revert/confirm/{{$transaction->id}}" class="btn btn-warning">Un-Delivered</a>
                        } --}}
                        @if(!auth()->user()->hasRole('hd_user'))
                            {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                        @endif
                        <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print Invoice</a>
                        @if(!$transaction->is_service)
                            <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary">Print DO</a>
                            <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary">Send Inv Email</a>
                        @endif
                        <a href="/transaction" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
            @elseif($state === 5)
            <div class="col-md-12">
                <div class="row">
                    <div class="pull-right">
                        @cannot('transaction_view')
                        @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            {!! Form::submit('Delete Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_wipe']) !!}
                            {!! Form::submit('Undo Cancel', ['class'=> 'btn btn-warning', 'form'=>'form_reverse', 'name'=>'form_reverse']) !!}
                            @if(!$transaction->deals()->exists() and !$transaction->is_service)
                                {!! Form::submit('Convert to Service', ['name'=>'is_service', 'class'=> 'btn btn-default', 'form'=>'form_cust']) !!}
                            @endif
                        @endif
                        @endcan
                        <a href="/transaction" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
            @elseif($state === 6)
            <div class="col-md-12">
                <div class="row">
                    <div class="pull-left">
                        {{-- @can('transaction_deleteitem') --}}
                        @cannot('transaction_view')
                        @cannot('supervisor_view')
                        @if(!auth()->user()->hasRole('franchisee')and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                            {!! Form::submit('Cancel Invoice', ['class'=> 'btn btn-danger', 'form'=>'form_delete', 'name'=>'form_delete']) !!}
                            {!! Form::submit('Unpaid', ['name'=>'unpaid', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                        @endif
                        @endcannot
                        @endcannot
                        {{-- @endcan --}}
                    </div>
                    <div class="pull-right">
                        @if(!auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        {!! Form::submit('Update', ['name'=>'update', 'class'=> 'btn btn-warning', 'form'=>'form_cust']) !!}
                        @endif
                        <a href="/transaction/emailInv/{{$transaction->id}}" class="btn btn-primary">Send Inv Email</a>
                        <a href="/transaction/download/{{$transaction->id}}" class="btn btn-primary">Print Invoice</a>
                        {{-- @if($transaction->is_deliveryorder) --}}
                            <a href="/transaction/download/{{$transaction->id}}?value=do" class="btn btn-primary">Print DO</a>
                        {{-- @endif --}}
                        <a href="/transaction" class="btn btn-default">Back</a>
                    </div>
                </div>
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

    <div class="panel-footer">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Printable Attachment(s)
            </div>
            <div class="panel-body">
                {!! Form::open(['action'=>['TransactionController@updateFilesName', $transaction->id]]) !!}
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