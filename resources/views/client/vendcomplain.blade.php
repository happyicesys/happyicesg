@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<div class="row" style="padding: 50px 0px 30px 0px;">
    <div class="col-md-12 col-sm-12 col-xs 12">
        <div class="col-md-10 col-md-offset-1" style="padding-top:25px;">
            <span style="font-size: 15px;">
            **If your item failed to dispense, please fill in the form, we will respond you ASAP
            </span>

            <div style="padding-top: 20px; font-size: 16px;">
                {!! Form::open(['action'=>'ClientController@sendVendingComplain']) !!}
                    {!! Honeypot::generate('my_name', 'my_time') !!}
                    <div class="form-group">
                        <span style="color:red">*</span>
                        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                        {!! Form::text('name', null, ['class'=>'form-control']) !!}
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                            <span style="color:red">*</span>
                            {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                            {!! Form::text('email', null, ['class'=>'form-control']) !!}
                        </div>

                        <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                            {!! Form::label('contact', 'Contact Number', ['class'=>'control-label']) !!}
                            {!! Form::text('contact', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                            <span style="color:red">*</span>
                            {!! Form::label('vend_id', 'Vending ID', ['class'=>'control-label']) !!}
                            {!! Form::text('vend_id', null, ['class'=>'form-control', 'placeholder'=>'Please enter the vending machine id']) !!}
                        </div>

                        <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                            <span style="color:red">*</span>
                            {!! Form::label('product_id', 'Product ID', ['class'=>'control-label']) !!}
                            {!! Form::text('product_id', null, ['class'=>'form-control', 'placeholder'=>'Please enter the product id']) !!}
                        </div>
                    </div>                    

                    <div class="form-group">
                        <span style="color:red">*</span>
                        {!! Form::label('message', 'Your Message', ['class'=>'control-label']) !!}
                        {!! Form::textarea('message', null, ['class'=>'form-control', 'rows'=>'5', 'placeholder'=>'Your message']) !!}
                    </div>

                    {!! Form::submit('Submit', ['class'=> 'btn btn-lg btn-success']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script>
    
</script>

@stop