@inject('people', 'App\Person')
@inject('users', 'App\User')
@inject('personassets', 'App\Personasset')
@inject('transactionpersonassets', 'App\Transactionpersonasset')

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
                    {!! Form::text('submission_datetime',  null, ['class'=>'form-control', 'readonly'=>'readonly', 'ng-model'=>'doform.submission_datetime']) !!}
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
        @if(!$transaction->is_deliveryorder)
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
                <label for="required" class="control-label" style="color:red;">*</label>
                {!! Form::select('job_type',
                    [''=>null, 'Delivery_Job'=>'Delivery Job', 'OnSite_Troubleshooting'=>'OnSite Troubleshooting'],
                    $transaction->deliveryorder->job_type,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'doform.job_type'
                    ])
                !!}
            </div>
            <div class="col-md-4 form-group">
                {!! Form::label('po_no', 'PO Number', ['class'=>'control-label']) !!}
                <label for="required" class="control-label" style="color:red;">*</label>
                {!! Form::select('po_no',
                    [''=>null, '4505160978_(FSI)'=>'4505160978 (FSI)', '4505160966_(Retail)'=>'4505160966 (Retail)'],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'doform.po_no'
                    ])
                !!}
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
                            [
                                'class'=>'person form-control'
                             ])
                        !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_attn', 'Contact Person', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('pickup_attn', null, ['class'=>'form-control', 'ng-model'=>'doform.pickup_attn']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_contact', 'Tel No.', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('pickup_contact', $transaction->deliveryorder->pickup_contact, ['class'=>'form-control', 'ng-model'=>'doform.pickup_contact']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_postcode', 'Pickup Postcode', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('pickup_postcode', $transaction->deliveryorder->pickup_postcode, ['class'=>'form-control', 'ng-model'=>'doform.pickup_postcode']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('from_happyice', 'From Happyice', ['class'=>'control-label']) !!}
                        <br>
                        {!! Form::checkbox('from_happyice', $transaction->deliveryorder->from_happyice, null, ['ng-model'=>'doform.from_happyice', 'ng-change'=>'onFromHappyiceChanged()']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('pickup_address', 'Pickup Address', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::textarea('pickup_address', $transaction->deliveryorder->pickup_address, ['class'=>'form-control', 'rows'=>'3', 'ng-model'=>'doform.pickup_address']) !!}
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
                        {!! Form::text('delivery_attn', $transaction->deliveryorder->delivery_attn, ['class'=>'form-control', 'ng-model'=>'doform.delivery_attn']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_contact', 'Tel No.', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('delivery_contact', $transaction->deliveryorder->delivery_contact, ['class'=>'form-control', 'ng-model'=>'doform.delivery_contact']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::text('delivery_postcode', $transaction->deliveryorder->delivery_postcode, ['class'=>'form-control', 'ng-model'=>'doform.delivery_postcode']) !!}
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('to_happyice', 'To Happyice', ['class'=>'control-label']) !!}
                        <br>
                        {!! Form::checkbox('to_happyice', $transaction->deliveryorder->to_happyice, null, ['ng-model'=>'doform.to_happyice', 'ng-change'=>'onToHappyiceChanged()']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_address', 'Delivery Address', ['class'=>'control-label']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::textarea('delivery_address', $transaction->deliveryorder->delivery_address, ['class'=>'form-control', 'rows'=>'3', 'ng-model'=>'doform.delivery_address']) !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('delivery_comment', 'Comment', ['class'=>'control-label']) !!}
                        {!! Form::textarea('delivery_comment', $transaction->deliveryorder->delivery_comment, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>
                </div>
            </div>

        <div>
            <div style="width: 100%; height: 20px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
              <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                Asset and Quantity
              </span>
            </div>
            <div ng-show="showpersonassetSelection">
                @if(auth()->user()->hasRole('admin') or (auth()->user()->hasRole('hd_user') and $transaction->status == 'Pending'))
                <div class="row">
                    <div class="form-group col-md-7 col-sm-7 col-xs-12">
                        {!! Form::label('personasset_id', 'Asset', ['class'=>'control-label search-title']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::select('personasset_id',
                            [''=>null] + $personassets::select(DB::raw("CONCAT(code,' - ',name,'  [',brand,']') AS full, id"))->orderBy('code')->lists('full', 'id')->all(),
                            null,
                            [
                            'id'=>'personasset_id',
                            'class'=>'selectassetform form-control',
                            'ng-model'=>'assetform.personasset_id'
                            ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-3 col-xs-12">
                        {!! Form::label('personasset_qty', 'Qty', ['class'=>'control-label search-title']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::select('personasset_qty',
                            [
                                '' => null,
                                '1' => 1,
                                '2' => 2,
                                '3' => 3,
                                '4' => 4,
                                '5' => 5,
                                '6' => 6,
                                '7' => 7,
                                '8' => 8,
                                '9' => 9,
                                '10' => 10,
                            ],
                            null,
                            [
                            'id'=>'personasset_qty',
                            'class'=>'selectassetform form-control',
                            'ng-model'=>'assetform.personasset_qty',
                            'ng-change'=> 'onAssetqtyChanged()'
                            ])
                        !!}
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-12" style="padding-top: 25px;">
                        <button type="button" class="btn btn-success btn-block" ng-disabled="!assetform.personasset_id || !assetform.personasset_qty" ng-click="submitTransactionpersonasset()">Add</button>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12" style="border: 1px solid black;" ng-repeat="(index ,assetformitem) in assetformitems">
                        <div class="form-group">
                            {!! Form::label('serial_no', 'Serial No', ['class'=>'control-label']) !!}
                            {!! Form::text('serial_no', null, ['class'=>'form-control', 'ng-model'=>'assetformitem.serial_no']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('sticker', 'Sticker', ['class'=>'control-label']) !!}
                            {!! Form::text('sticker', null, ['class'=>'form-control', 'ng-model'=>'assetformitem.sticker']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div ng-show="!showpersonassetSelection">
                @if(auth()->user()->hasRole('admin') or (auth()->user()->hasRole('hd_user') and $transaction->status == 'Pending'))
                <div class="row">
                    <div class="form-group col-md-10 col-sm-10 col-xs-12">
                        {!! Form::label('transactionpersonasset_id', 'Asset', ['class'=>'control-label search-title']) !!}
                        <label for="required" class="control-label" style="color:red;">*</label>
                        {!! Form::select('transactionpersonasset_id',
                            [''=>null] + $transactionpersonassets::leftJoin('personassets', 'personassets.id', '=', 'transactionpersonassets.personasset_id')->select(DB::raw("CONCAT(code,' - ',name,'  [',brand,'] - ', serial_no, ' ', sticker) AS full, transactionpersonassets.id"))->orderBy('code')->lists('full', 'id')->all(),
                            null,
                            [
                            'id'=>'transactionpersonasset_id',
                            'class'=>'selectassetform form-control',
                            'ng-model'=>'assetform.transactionpersonasset_id'
                            ])
                        !!}
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-12" style="padding-top: 25px;">
                        <button type="button" class="btn btn-success btn-block" ng-disabled="!assetform.transactionpersonasset_id" ng-click="submitTransactionpersonasset()">Add</button>
                    </div>
                </div>
                @endif
            </div>

            <div class="table-responsive" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('personasset_code')">
                            Code
                            <span ng-if="search.sortName == 'personasset_code' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'personasset_code' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('personasset_name')">
                            Name
                            <span ng-if="search.sortName == 'personasset_name' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'personasset_name' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('personasset_brand')">
                            Brand
                            <span ng-if="search.sortName == 'personasset_brand' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'personasset_brand' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('serial_no')">
                            Serial No
                            <span ng-if="search.sortName == 'serial_no' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'serial_no' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('sticker')">
                            Sticker
                            <span ng-if="search.sortName == 'sticker' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'sticker' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr ng-repeat="data in alldata">
                            <td class="col-md-1 text-center">
                                @{{ $index + 1 }}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.code}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.name}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.brand}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.serial_no}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.sticker}}
                            </td>
                            <td class="col-md-1 text-center">
                                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('hd_user'))
                                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#transactionpersonasset_modal" ng-click="editTransactionpersonassetModal($event, data)" ng-disabled="{{auth()->user()->hasRole('hd_user') && $transaction->status != 'Pending'}}"><i class="fa fa-pencil-square-o"></i></button>
                                    <button class="btn btn-danger btn-sm" ng-click="removeTransactionpersonassetEntry($event, data.id)" ng-disabled="{{auth()->user()->hasRole('hd_user') && $transaction->status != 'Pending'}}"><i class="fa fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                        <tr ng-if="!alldata || alldata.length == 0">
                            <td colspan="18" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="transactionpersonasset_modal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                            Edit Entries
                        </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Code
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="transactionpersonassetform.code" readonly>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Name
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="transactionpersonassetform.name" readonly>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Brand
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="transactionpersonassetform.brand" readonly>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Serial No
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="transactionpersonassetform.serial_no">
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Sticker
                                    </label>
                                    <textarea name="specs3" class="form-control" rows="3" ng-model="transactionpersonassetform.sticker"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" ng-click="updateTransactionpersonasset($event)" data-dismiss="modal">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

