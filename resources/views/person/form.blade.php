@inject('payterm', 'App\Payterm')
@inject('profiles', 'App\Profile')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('cust_id', 'ID', ['class'=>'control-label']) !!}
            {!! Form::text('cust_id', null, ['class'=>'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('site_name', 'Site Name', ['class'=>'control-label']) !!}
            {!! Form::text('site_name', null, ['class'=>'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('company', 'ID Name', ['class'=>'control-label']) !!}
            {!! Form::text('company', null, ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('com_remark', 'Company', ['class'=>'control-label']) !!}
            {!! Form::text('com_remark', null, ['class'=>'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('bill_address', 'Billing Address', ['class'=>'control-label']) !!}
            {!! Form::textarea('bill_address', null, ['class'=>'form-control', 'rows'=>'2']) !!}
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('del_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
            {!! Form::text('del_postcode', null, ['class'=>'form-control', 'id'=>'del_postcode']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('del_address', 'Delivery Address', ['class'=>'control-label']) !!}
            {!! Form::textarea('del_address', null, ['id'=>'del_address', 'class'=>'form-control', 'rows'=>'2']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('name', 'Att To', ['class'=>'control-label']) !!}
            {!! Form::text('name', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
            {!! Form::text('contact', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('alt_contact', 'Alt Contact', ['class'=>'control-label']) !!}
            {!! Form::text('alt_contact', null, ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
            {!! Form::textarea('email', null, ['class'=>'form-control', 'rows'=>'3']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('cost_rate', 'Cost Rate (%)', ['class'=>'control-label']) !!}
            {!! Form::text('cost_rate', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('payterm', 'Terms', ['class'=>'control-label']) !!}
            {!! Form::select('payterm', $payterm::lists('name', 'name'), null, ['id'=>'payterm', 'class'=>'select form-control']) !!}
        </div>
    </div>
</div>

@php
    $franchisee = null;
    if(auth()->user()->hasRole('franchisee')) {
        $franchisee = auth()->user()->id;
    }
@endphp

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            {!! Form::label('franchisee_id', 'Franchisee/ Leasee', ['class'=>'control-label']) !!}
            {!! Form::select('franchisee_id',
                            [''=> null] + $franchisees::filterUserFranchise()->pluck('name', 'id')->all(),
                            $franchisee,
                            ['id'=>'franchisee_id', 'class'=>'select form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('profile_id', 'Company Profile', ['class'=>'control-label']) !!}
            {!! Form::select('profile_id',
                            $profiles::filterUserProfile()->select(DB::raw("CONCAT(name,' - ',roc_no) AS full, id"))->pluck('full', 'id'),
                            null, ['id'=>'profile_id', 'class'=>'select form-control']) !!}
        </div>
    </div>
    @if($person->profile)
        @if($person->profile->gst)
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="form-group" style="padding-top: 25px;">
                    {!! Form::checkbox('is_gst_inclusive', $person->is_gst_inclusive) !!}
                    {!! Form::label('is_gst_inclusive', 'GST Inclusive (Default: '.($person->profile->is_gst_inclusive ? 'Inclusive)' : 'Exclusive)'), ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
                </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="form-group">
                    {!! Form::label('gst_rate', 'GST Rate % (Default: '.($person->profile->gst_rate + 0).')', ['class'=>'control-label']) !!}
                    {!! Form::text('gst_rate', $person->gst_rate, ['class'=>'form-control']) !!}
                </div>
            </div>
        @endif
    @endif
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-8">
        <div class="form-group">
            {!! Form::label('custcategory_id', 'Customer Category', ['class'=>'control-label']) !!}
            {!! Form::select('custcategory_id', [null=>''] + $custcategories::orderBy('name')->pluck('name', 'id')->all(), null, ['class'=>'select form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
        <div class="form-group" style="padding-top: 25px;">
            {!! Form::checkbox('is_dvm', $person->is_dvm) !!}
            {!! Form::label('is_dvm', 'Direct Vending Machine', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-6">
        <div class="form-group" style="padding-top: 25px;">
            {!! Form::checkbox('is_vending', $person->is_vending) !!}
            {!! Form::label('is_vending', 'Fun Vending Machine', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
            {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'3']) !!}
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('operation_note', 'Ops Note', ['class'=>'control-label']) !!}
            {!! Form::textarea('operation_note', null, ['class'=>'form-control', 'rows'=>'3']) !!}
        </div>
    </div>
</div>

