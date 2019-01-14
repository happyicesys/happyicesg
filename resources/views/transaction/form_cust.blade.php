@inject('people', 'App\Person')
@inject('users', 'App\User')

<div class="panel panel-primary">
    <div class="panel-body">
        <label style="margin-bottom: 15px; font-size: 18px;">
            <a href="/person/@{{ form.person }}">
                {{$transaction->person->cust_id}} - {{$transaction->person->company}}
            </a>
        </label>
{{--            @endif --}}
        {!! Form::text('person_id', '@{{form.person}}', ['class'=>'hidden form-control']) !!}
        {!! Form::text('person_copyid', '@{{form.person}}', ['class'=>'hidden form-control']) !!}
        {!! Form::text('person_code', '@{{form.cust_id}}', ['class'=>'hidden form-control']) !!}
        {!! Form::text('name', '@{{form.name}}', ['class'=>'hidden form-control']) !!}

        <div class="row">
            <div class="col-md-4 form-group">
                {!! Form::label('bill_address', 'Bill To', ['class'=>'control-label']) !!}
                {!! Form::textarea('bill_address', null, ['class'=>'form-control',
                'ng-model'=>'form.bill_address',
                'disabled'=> $disabled,
                'rows'=>'5']) !!}
            </div>

            {{-- haagen daz user disable --}}
            @if(!$transaction->is_deliveryorder)
                @if($transaction->status == 'Cancelled' or (Auth::user()->can('transaction_view') and $transaction->status === 'Delivered'))
                    <div class="col-md-4 form-group">
                        {!! Form::label('del_address', 'Delivery Add', ['class'=>'control-label']) !!}
                        {!! Form::textarea('del_address', null, ['class'=>'form-control input-sm',
                        'ng-model'=>'form.del_address',
                        'readonly'=>'readonly',
                        'rows'=>'5']) !!}
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('transremark', 'Remark', ['class'=>'control-label']) !!}
                        {!! Form::textarea('transremark', null, ['class'=>'form-control', 'rows'=>'3', 'readonly'=>'readonly']) !!}
                    </div>
                @else
                    <div class="col-md-4 form-group">
                        {!! Form::label('del_address', 'Delivery Add', ['class'=>'control-label']) !!}
                        {!! Form::textarea('del_address', null, ['class'=>'form-control',
                        'ng-model'=>'form.del_address',
                        'disabled'=> $disabled,
                        'rows'=>'5']) !!}
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('transremark', 'Remark', ['class'=>'control-label']) !!}
                        {!! Form::textarea('transremark', null, ['class'=>'form-control',
                        'ng-model'=>'form.transremark',
                        'disabled'=> $disabled,
                        'rows'=>'5']) !!}
                    </div>
                @endif
            @endif
        </div>


        <div class="row">
        @if(!$transaction->is_deliveryorder)
        @if($transaction->status == 'Cancelled' or (Auth::user()->can('transaction_view') and $transaction->status === 'Delivered'))
            <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                {!! Form::label('form.order_date', 'Order On', ['class'=>'control-label']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            type = "text"
                            class = "form-control"
                            placeholder = "Order Date"
                            ng-model = "form.order_date"
                            ng-change = "dateChanged('order_date', form.order_date)"
                            readonly
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('order_date', form.order_date)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('order_date', form.order_date)"></span>
                </div>
            </div>

            <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                {!! Form::label('form.delivery_date', 'Delivery On', ['class'=>'control-label']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            type = "text"
                            class = "form-control"
                            placeholder = "Delivery Date"
                            ng-model = "form.delivery_date"
                            ng-change = "dateChanged('delivery_date', form.delivery_date)"
                            readonly
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_date', form.delivery_date)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_date', form.delivery_date)"></span>
                </div>
            </div>
        @else
            <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                {!! Form::label('form.order_date', 'Order On', ['class'=>'control-label']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            type = "text"
                            name = "order_date"
                            class = "form-control"
                            placeholder = "Order Date"
                            ng-model = "form.order_date"
                            ng-change = "dateChanged('order_date', form.order_date)"
                            {{$disabledStr}}
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('order_date', form.order_date)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('order_date', form.order_date)"></span>
                </div>
            </div>

            <div class="col-md-3 col-sm-12 col-xs-12 form-group">
                {!! Form::label('form.delivery_date', 'Delivery On', ['class'=>'control-label']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            type = "text"
                            name = "delivery_date"
                            class = "form-control"
                            placeholder = "Delivery Date"
                            ng-model = "form.delivery_date"
                            ng-change = "dateChanged('delivery_date', form.delivery_date)"
                            {{$disabledStr}}
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_date', form.delivery_date)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_date', form.delivery_date)"></span>
                </div>
            </div>
        @endif
        @endif

            <div class="col-md-3 col-xs-6 form-group">
                {!! Form::label('payterm', 'Pay Term', ['class'=>'control-label']) !!}
                {!! Form::textarea('payterm', null, ['class'=>'form-control',
                'ng-model'=>'form.payterm',
                'readonly'=>'readonly',
                'rows'=>'1']) !!}
            </div>

            @if($transaction->is_deliveryorder)
                <div class="col-md-3 col-xs-6 form-group">
                    {!! Form::label('created_by', 'Requested By', ['class'=>'control-label']) !!}
                    {!! Form::text('created_by', \App\User::find($transaction->deliveryorder->requester)->name, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                </div>

                @if($transaction->status != 'Pending')
                <div class="col-md-3 col-xs-6 form-group">
                    {!! Form::label('submission_datetime', 'Submission Datetime', ['class'=>'control-label']) !!}
                    {!! Form::text('submission_datetime',  \Carbon\Carbon::parse($transaction->deliveryorder->submission_datetime)->format('Y-m-d  h:i A'), ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                </div>
                @endif
            @endif

            @if(!$transaction->is_deliveryorder)
            @if($transaction->status == 'Cancelled' or (Auth::user()->can('transaction_view') and $transaction->status === 'Delivered'))
                <div class="col-md-3 col-xs-6 form-group">
                    {!! Form::label('del_postcode', 'PostCode', ['class'=>'control-label']) !!}
                    {!! Form::text('del_postcode', null, ['class'=>'form-control',
                    'ng-model'=>'form.del_postcode',
                    'disabled'=> $disabled,
                    'readonly'=>'readonly']) !!}
                </div>
            @else
                <div class="col-md-3 col-xs-6 form-group">
                    {!! Form::label('del_postcode', 'PostCode', ['class'=>'control-label']) !!}
                    {!! Form::text('del_postcode', null, [
                        'class'=>'form-control',
                        'disabled'=> $disabled,
                    'ng-model'=>'form.del_postcode']) !!}
                </div>
            @endif
            @endif
        </div>

        <div class="row">
            @if($transaction->status == 'Cancelled' or (Auth::user()->can('transaction_view') and $transaction->status === 'Delivered'))
                <div class="col-md-4 form-group">
                    {!! Form::label('po_no', 'PO #', ['class'=>'control-label']) !!}
                    {!! Form::text('po_no', null, ['class'=>'form-control',
                    'disabled'=> $disabled,
                    'readonly'=>'readonly'
                    ]) !!}
                </div>

                <div class="col-md-4 form-group">
                    {!! Form::label('name', 'Attn. Name', ['class'=>'control-label']) !!}
                    {!! Form::text('name', null, ['class'=>'form-control',
                    'ng-model'=>'form.attn_name',
                    'disabled'=> $disabled,
                    'readonly'=>'readonly'
                    ]) !!}
                </div>

                <div class="col-md-4 form-group">
                    {!! Form::label('contact', 'Tel No.', ['class'=>'control-label']) !!}
                    {!! Form::text('contact', null, ['class'=>'form-control',
                    'ng-model'=>'form.contact',
                    'disabled'=> $disabled,
                    'readonly'=>'readonly'
                    ]) !!}
                </div>
            @else
                <div class="col-md-4 form-group">
                    {!! Form::label('po_no', 'PO #', ['class'=>'control-label']) !!}
                    {!! Form::text('po_no', null, ['class'=>'form-control',
                    'disabled'=> $disabled]) !!}
                </div>

                <div class="col-md-4 form-group">
                    {!! Form::label('name', 'Attn. Name', ['class'=>'control-label']) !!}
                    {!! Form::text('name', null, ['class'=>'form-control',
                    'disabled'=> $disabled,
                    'ng-model'=>'form.attn_name']) !!}
                </div>

                <div class="col-md-4 form-group">
                    {!! Form::label('contact', 'Tel No.', ['class'=>'control-label']) !!}
                    {!! Form::text('contact', null, ['class'=>'form-control',
                    'disabled'=> $disabled,
                    'ng-model'=>'form.contact']) !!}
                </div>
            @endif
        </div>

        @if(!$transaction->is_deliveryorder)
        <div class="row">
            @if($transaction->status === 'Confirmed' or $transaction->status ==='Delivered' or $transaction->status === 'Verified Owe' or $transaction->status === 'Verified Paid')
                @cannot('transaction_view')
                    <div class="col-md-4 form-group">
                        {!! Form::label('driver', 'Delivered By', ['class'=>'control-label']) !!}
                        {!! Form::select('driver',
                                [''=>null]+$users::lists('name', 'name')->all(),
                                null,
                                ['class'=>'select form-control', 'disabled'=> $disabled,])
                        !!}
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('paid_by', 'Payment Received By', ['class'=>'control-label']) !!}
                        {!! Form::select('paid_by',
                                [''=>null]+$users::lists('name', 'name')->all(),
                                null,
                                ['class'=>'select form-control', 'disabled'=> $disabled])
                        !!}
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('paid_at', 'Payment Received On', ['class'=>'control-label']) !!}
                    <div class="input-group date">
                        {!! Form::text('paid_at', null, ['class'=>'form-control', 'id'=>'paid_at', 'disabled'=> $disabled,]) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                    </div>
                @endcannot
            @endif

            @if($transaction->status === 'Verified Paid' or $transaction->pay_status === 'Paid')
                <div class="col-md-4 form-group">
                    {!! Form::label('pay_method', 'Payment Method', ['class'=>'control-label']) !!}
                    {!! Form::select('pay_method',
                        [''=>null, 'cash'=>'Cash', 'cheque'=>'Cheque', 'tt'=>'Tt'],
                        null,
                        ['class'=>'select form-control', 'disabled'=> $disabled])
                    !!}
                </div>

                <div class="col-md-4 form-group">
                    {!! Form::label('note', 'Note', ['class'=>'control-label']) !!}
                    {!! Form::text('note', null, ['class'=>'form-control', 'disabled'=> $disabled]) !!}
                </div>
            @endif
        </div>
        @endif

        @if($transaction->is_deliveryorder)
        <div class="row">
            <div class="col-md-4 form-group">
                {!! Form::label('job_type', 'Job Type', ['class'=>'control-label']) !!}
                {!! Form::select('job_type',
                    ['Delivery_Job'=>'Delivery Job', 'OnSite_Troubleshooting'=>'OnSite Troubleshooting'],
                    $transaction->deliveryorder->job_type,
                    ['class'=>'select form-control'])
                !!}
            </div>
            <div class="col-md-4 form-group">
                {!! Form::label('po_no', 'PO Number', ['class'=>'control-label']) !!}
                {!! Form::select('po_no',
                    ['4505160978_(FSI)'=>'4505160978 (FSI)', '4505160966_(Retail)'=>'4505160966 (Retail)'],
                    $transaction->deliveryorder->po_no,
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
        @endif

        @if($transaction->is_deliveryorder)
            <div style="width: 100%; height: 20px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
              <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                Freezer Type and Quantity
              </span>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                    {!! Form::label('freezer1', 'Upright Freezer (Hiron)', ['class'=>'control-label']) !!}
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="freezer[1]">
                                <span class="glyphicon glyphicon-minus"></span>
                            </button>
                        </span>
                        <input type="text" name="freezer[1]" class="form-control input-number text-center" value="1" min="1" max="10">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="freezer[1]">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                    {!! Form::label('freezer2', 'Upright Freezer (Framec)', ['class'=>'control-label']) !!}
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="freezer[2]">
                                <span class="glyphicon glyphicon-minus"></span>
                            </button>
                        </span>
                        <input type="text" name="freezer[2]" class="form-control input-number text-center" value="1" min="1" max="10">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="freezer[2]">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                    {!! Form::label('freezer3', 'Cheest Freezer', ['class'=>'control-label']) !!}
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="freezer[3]">
                                <span class="glyphicon glyphicon-minus"></span>
                            </button>
                        </span>
                        <input type="text" name="freezer[3]" class="form-control input-number text-center" value="1" min="1" max="10">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="freezer[3]">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                    {!! Form::label('freezer4', 'Counter Top Freezer', ['class'=>'control-label']) !!}
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="freezer[4]">
                                <span class="glyphicon glyphicon-minus"></span>
                            </button>
                        </span>
                        <input type="text" name="freezer[4]" class="form-control input-number text-center" value="1" min="1" max="10">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="freezer[4]">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        @endif


        @if(($transaction->person->is_vending === 1 or $transaction->person->is_dvm === 1) and !$transaction->is_deliveryorder)
            <div style="width: 100%; height: 20px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
              <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                Vending Machine
              </span>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('digital_clock', 'Digital Clocker', ['class'=>'control-label']) !!}
                        {!! Form::text('digital_clock', null, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    @cannot('transaction_view')
                    <div class="col-md-8 col-sm-8 col-xs-8">
                    @endcannot
                        <div class="form-group">
                            {!! Form::label('analog_clock', 'Analog Clocker', ['class'=>'control-label']) !!}
                            {!! Form::text('analog_clock', null, ['class'=>'form-control']) !!}
                        </div>
                    @cannot('transaction_view')
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4" style="padding-top: 25px;">
                            {!! Form::checkbox('is_required_analog', $transaction->is_required_analog) !!}
                            <small>{!! Form::label('is_required_analog', 'Required', ['class'=>'control-label']) !!}</small>
                    </div>
                    @endcannot
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('balance_coin', 'Balance Coin', ['class'=>'control-label']) !!}
                        {!! Form::text('balance_coin', null, ['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
            @if($transaction->person->is_dvm)
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('sales_count', 'Sales Count (pcs)', ['class'=>'control-label']) !!}
                        {!! Form::text('sales_count', null, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('sales_amount', 'Sales Amount ($)', ['class'=>'control-label']) !!}
                        {!! Form::text('sales_amount', null, ['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
            @endif
        @endif

        @if($transaction->is_deliveryorder)
            <div style="width: 100%; height: 20px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
              <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                Pick-up Detail
              </span>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                    {!! Form::label('pickup_date', 'Pickup Date', ['class'=>'control-label']) !!}
                    <label for="required" class="control-label" style="color:red;">*</label>
                    <div class="input-group date">
                        {!! Form::text('pickup_date', $transaction->deliveryorder->pickup_date ? $transaction->deliveryorder->pickup_date : \Carbon\Carbon::today(), ['class'=>'form-control', 'id'=>'pickup_date']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_timerange', 'Time Range', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::select('pickup_timerange',
                            [
                                'Anytime'=>'Anytime',
                                '9am-12pm' => '9am-12pm',
                                '12pm-5pm' => '12pm-5pm',
                                '5pm-10pm' => '5pm-10pm',
                                '5pm-10pm' => '5pm-10pm',
                                'Anytime_before_5pm' => 'Anytime before 5pm',
                            ],
                            $transaction->deliveryorder->pickup_timerange,
                            ['class'=>'person form-control'])
                        !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_attn', 'Contact Person', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('pickup_attn', $transaction->deliveryorder->pickup_attn, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_contact', 'Tel No.', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('pickup_contact', $transaction->deliveryorder->pickup_contact, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_postcode', 'Pickup Postcode', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('pickup_postcode', $transaction->deliveryorder->pickup_postcode, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_address', 'Pickup Address', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::textarea('pickup_address', $transaction->deliveryorder->pickup_address, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_comment', 'Comment', ['class'=>'control-label']) !!}
                        {!! Form::textarea('pickup_comment', $transaction->deliveryorder->pickup_comment, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>
                </div>
            </div>


            <div style="width: 100%; height: 20px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
              <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                Delivery Detail
              </span>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                    {!! Form::label('delivery_date1', 'Delivery Date', ['class'=>'control-label']) !!}
                    <label for="required" class="control-label" style="color:red;">*</label>
                    <div class="input-group date">
                        {!! Form::text('delivery_date1', $transaction->deliveryorder->delivery_date1 ? $transaction->deliveryorder->delivery_date1 : \Carbon\Carbon::today(), ['class'=>'form-control', 'id'=>'delivery_date1']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_timerange', 'Time Range', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::select('delivery_timerange',
                            [
                                'Anytime'=>'Anytime',
                                '9am-12pm' => '9am-12pm',
                                '12pm-5pm' => '12pm-5pm',
                                '5pm-10pm' => '5pm-10pm',
                                '5pm-10pm' => '5pm-10pm',
                                'Anytime before 5pm' => 'Anytime before 5pm',
                            ],
                            $transaction->deliveryorder->delivery_timerange,
                            ['class'=>'person form-control'])
                        !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_attn', 'Contact Person', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('delivery_attn', $transaction->deliveryorder->delivery_attn, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_contact', 'Tel No.', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('delivery_contact', $transaction->deliveryorder->delivery_contact, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('delivery_postcode', $transaction->deliveryorder->delivery_postcode, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_address', 'Delivery Address', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::textarea('delivery_address', $transaction->deliveryorder->delivery_address, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_comment', 'Comment', ['class'=>'control-label']) !!}
                        {!! Form::textarea('delivery_comment', $transaction->deliveryorder->delivery_comment, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

