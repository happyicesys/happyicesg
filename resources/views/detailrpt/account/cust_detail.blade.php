<div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
            {!! Form::select('profile_id', [''=>'All']+$profiles::lists('name', 'id')->all(), null,
                [
                'class'=>'select form-control',
                'ng-model'=>'search.profile_id',
                ])
            !!}
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
            <datepicker>
                <input
                    type = "text"
                    class = "form-control input-sm"
                    placeholder = "Delivery From"
                    ng-model = "search.delivery_from"
                />
            </datepicker>
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('payment_from', 'Payment From', ['class'=>'control-label search-title']) !!}
            <datepicker>
                <input
                    type = "text"
                    class = "form-control input-sm"
                    placeholder = "Payment From"
                    ng-model = "search.payment_from"
                />
            </datepicker>
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('id', 'ID', ['class'=>'control-label search-title']) !!}
            {!! Form::text('id', null,
                                        [
                                            'class'=>'form-control input-sm',
                                            'ng-model'=>'search.cust_id',
                                            'placeholder'=>'Cust ID',
                                        ])
            !!}
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
            <datepicker>
                <input
                    type = "text"
                    class = "form-control input-sm"
                    placeholder = "Delivery To"
                    ng-model = "search.delivery_to"
                />
            </datepicker>
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('payment_to', 'Payment To', ['class'=>'control-label search-title']) !!}
            <datepicker>
                <input
                    type = "text"
                    class = "form-control input-sm"
                    placeholder = "Payment To"
                    ng-model = "search.payment_to"
                />
            </datepicker>
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
            {!! Form::text('company', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.company',
                                                'placeholder'=>'ID Name',
                                            ])
            !!}
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
            {!! Form::select('status', [''=>null, 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], null,
                [
                'class'=>'select form-control',
                'ng-model'=>'search.status',
                ])
            !!}
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('customer', 'Customer', ['class'=>'control-label search-title']) !!}
            {!! Form::select('customer',
                [''=>null] + $customers::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                null,
                [
                'class'=>'select form-control',
                'ng-model'=>'search.customer',
                ])
            !!}
        </div>
        <div class="form-group col-md-4 col-sm-4 col-xs-6">
            {!! Form::label('payment', 'Payment', ['class'=>'control-label search-title']) !!}
            {!! Form::select('payment',
                [''=>null, 'Paid'=>'Paid', 'Owe'=>'Owe'],
                null,
                [
                'class'=>'select form-control',
                'ng-model'=>'search.payment',
                ])
            !!}
        </div>
    </div>
</div>