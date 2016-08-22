@inject('payterm', 'App\Payterm')
@inject('profiles', 'App\Profile')

<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('cust_id', 'ID', ['class'=>'control-label']) !!}
        {!! Form::text('cust_id', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('company', 'ID Name', ['class'=>'control-label']) !!}
        {!! Form::text('company', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('com_remark', 'Company', ['class'=>'control-label']) !!}
        {!! Form::text('com_remark', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('bill_address', 'Billing Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('bill_address', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('name', 'Att To', ['class'=>'control-label']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('alt_contact', 'Alt Contact', ['class'=>'control-label']) !!}
        {!! Form::text('alt_contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::email('email', null, ['class'=>'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('site_name', 'Site Name', ['class'=>'control-label']) !!}
        {!! Form::text('site_name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('del_address', 'Delivery Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('del_address', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('del_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
        {!! Form::text('del_postcode', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('cost_rate', 'Cost Rate (%)', ['class'=>'control-label']) !!}
        {!! Form::text('cost_rate', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('payterm', 'Terms', ['class'=>'control-label']) !!}
        {!! Form::select('payterm', $payterm::lists('name', 'name'), null, ['id'=>'payterm', 'class'=>'select form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('profile_id', 'Company Profile', ['class'=>'control-label']) !!}
        {!! Form::select('profile_id',
                        $profiles::select(DB::raw("CONCAT(name,' - ',roc_no) AS full, id"))->lists('full', 'id'),
                        null, ['id'=>'profile_id', 'class'=>'select form-control']) !!}
    </div>
</div>

<script>
    $('.select').select2();
</script>
