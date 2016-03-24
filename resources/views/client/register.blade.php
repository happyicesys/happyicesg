@extends('template_client')
@section('title')
Sign Up
@stop
@section('content')

<div class="col-md-8 col-md-offset-2">
{!! Form::model($client = new \App\Person, ['action'=>'ClientController@store']) !!}
    <div class="row">
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <span style="color:red">*</span>
                {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
            </div>
            <div class="col-md-6 col-sm-6 col-xs" style="padding-top:3px;">
                {!! Form::select('salutation',
                    [
                        'Mr.'=>'Mr.',
                        'Mrs.'=>'Mrs.',
                        'Miss'=>'Miss',
                    ],
                    null,
                    ['id'=>'salution', 'class'=>'select form-control'])
                !!}
            </div>
            <div class="col-md-6">
                {!! Form::text('name', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="form-group" style="padding-top: 10px;">
        <span style="color:red">*</span>
        {!! Form::label('contact', 'Contact Number', ['class'=>'control-label']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control', 'placeholder'=>'Digits Only']) !!}
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <span style="color:red">*</span>
                {!! Form::label('del_postcode', 'Postcode', ['class'=>'control-label']) !!}
                {!! Form::text('del_postcode', null, ['class'=>'form-control', 'placeholder'=>'6 Digits']) !!}
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('dob', 'Birth Date', ['class'=>'control-label']) !!}
                <div class="input-group date">
                {!! Form::text('dob', null, ['class'=>'form-control', 'id'=>'dob', 'placeholder'=>'Choose the date']) !!}
                <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <span style="color:red">*</span>
        {!! Form::label('del_address', 'Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('del_address', null, ['class'=>'form-control', 'rows'=>'2', 'placeholder'=>'eg. Unit-Floor/Blk, Rd']) !!}
    </div>

    <div class="form-group">
        <span style="color:red">*</span>
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::email('email', null, ['class'=>'form-control', 'placeholder'=>'eg. name@mail.com']) !!}
    </div>

    <div class="form-group">
        <span style="color:red">*</span>
        {!! Form::label('password', 'Password', ['class'=>'control-label']) !!}
        {!! Form::password('password', ['class'=>'form-control']) !!}
        <span style="color:grey"><small>At least of 6 characters</small></span>
    </div>

    <div class="form-group">
        <span style="color:red">*</span>
        {!! Form::label('password_confirmation', 'Password Confirmation', ['class'=>'control-label']) !!}
        {!! Form::password('password_confirmation', ['class'=>'form-control']) !!}
        <span style="color:grey"><small>Same with password</small></span>
    </div>

    <div class="form-group row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top:10px;">
            <button type="submit" class="btn btn-success outline" style="margin-right: 15px;  border-radius: 0px; width:100%; font-size:20px; font-weight:200; font-family:Helvetica;">
                <strong>Sign Up</strong>
            </button>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top:10px;">
            <div class="form-group">
                <a href="/client/login">Already a member? Login</a>
            </div>
        </div>
    </div>
{!! Form::close() !!}
</div>

<script>

    $('.select').select2();

    $('#dob').val('');

    $('.date').datetimepicker({
       format: 'DD-MMMM-YYYY'
    });
</script>
@stop