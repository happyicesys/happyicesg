@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<div class="row" style="padding: 70px 0px 30px 0px;">
    <div class="col-md-6 col-sm-6 col-xs-12 text-center">
        <span style="font-size: 18px; margin-bottom: 10px;">
            <strong>Happy Ice Logistics Pte Ltd</strong>
            <p>
            Blk 2021, #01-198, Bukit Batok St 23, 659526 <br>
            TEL: 6563 1692 / 9777 3533
            </p>
        </span>
{{--         <iframe width="600" height="450" frameborder="1" style="border:0"
        src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJTelMFXUQ2jERAjn-O6_JeCI&key=AIzaSyC3twP5qBnguWnEQIVAawJWPyqQvOfBi0Q" allowfullscreen>
        </iframe> --}}
        <iframe width="600" height="450" frameborder="1" style="border:0"
        src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJQZUySGsQ2jERUgq_TPWMnqg&key=AIzaSyC3twP5qBnguWnEQIVAawJWPyqQvOfBi0Q" allowfullscreen>
        </iframe>
    </div>

    <div class="col-md-6 col-sm-6 col-xs 12">
        <div class="col-md-10 col-md-offset-1" style="padding-top:25px;">
            <span style="font-size: 15px;">
            **Send us your inquiries or questions, we will do our best to get back to you ASAP
            </span>

            <div style="padding-top: 20px; font-size: 16px;">
                {!! Form::open(['action'=>'ClientController@sendContactEmail']) !!}
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