@inject('payterm', 'App\Payterm')

<div class="col-md-6">

    <div class="form-group">
        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
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

    <div class="form-group">
        {!! Form::label('payterm', 'Terms', ['class'=>'control-label']) !!}
        {!! Form::select('payterm', $payterm::lists('name', 'name'), null, ['id'=>'payterm', 'class'=>'select form-control']) !!}
    </div>

</div>

<div class="col-md-6">

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('block', 'Block', ['class'=>'control-label']) !!}
                {!! Form::text('block', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('floor', 'Floor', ['class'=>'control-label']) !!}
                {!! Form::text('floor', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                {!! Form::text('unit', null, ['class'=>'form-control']) !!}
            </div>
        </div>
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
        {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>

</div>

<script>
    $('.select').select2();
</script>
