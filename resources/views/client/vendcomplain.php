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

                    <div class="form-group">
                        <span style="color:red">*</span>
                        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                        {!! Form::text('email', null, ['class'=>'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('contact', 'Contact Number', ['class'=>'control-label']) !!}
                        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
                    </div>

                    <div class="form-group">
                        <span style="color:red">*</span>
                        {!! Form::label('subject', 'Subject', ['class'=>'control-label']) !!}
                        {!! Form::text('subject', null, ['class'=>'form-control']) !!}
                    </div>

                    <div class="form-group">
                        <span style="color:red">*</span>
                        {!! Form::label('message', 'Your Message', ['class'=>'control-label']) !!}
                        {!! Form::textarea('message', null, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>

                    {!! Form::submit('Submit', ['class'=> 'btn btn-lg btn-success']) !!}

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop