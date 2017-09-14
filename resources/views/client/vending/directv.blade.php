@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<section id="services" style="padding-top: 15px;">
    <div class="container">
        <h2 class="text-center">Happy Ice Direct Vending Machine</h2>
        <img class="center-block" src="/img/vending/direct_vending.png" class="img-responsive" style="max-height: 700px; max-width: 400px; margin-top: -70px;" alt="Direct Vending Machine">
        <div class="row" style="padding-top: 15px;">
            <p style="font-size: 18px;">
                Vending machine has been very popular in market for years, but vending machine that dispenses ice cream hardly can be found yet, due to its extra ordinary high cost.
            </p>

            <p style="font-size: 18px;">
                Happy Ice has invented a simple, small and cost effective ice cream vending machine that can be deployed in the market in a large scale.
            </p>

            <p style="font-size: 18px;">
                The machine takes up only a very small foot print size of 56cm x 85 cm, which is less than 0.5m2. This makes this machine an ideal candidate to be placed at retail shop which usually has very limited real estate. It has a holding capacity of 180 pieces of ice cream (can be extendable to be 360 pieces of ice cream). Its power consumption is in the range of 100 Watt, in contrast to other ice cream vending machine which usually consumes more than 1000 watt. There is no worry of ice cream melting should there be a power failure event as the freezer can hold the ice cream after power cut-off for up to 3 hours.
            </p>
        </div>

        <div class="row">
        <h2><span style="color:#9f6bb5;">Features:</span><span style="color:#E54E91;"> SMALL in size, BIG in capacity</span></h2>
        <div class="col-md-10 col-xs-12" style="border: black solid 1px; background-color: #F49AC1; font-size: 18px; padding: 20px 0px 20px 0px;">
            <ul>
                <li>Smallest vending machine in the market - 56(L) x 81(W) x 169(H) </li>
                <li>Maximum ice cream holding capacity of 180 pieces</li>
                <li>Up to 8 flavours selection, extendable to 32 flavours selecton (780 pieces, width increase to 2.5m)</li>
                <li>Ultra low power consumption of 100 Watt - as low as $15 per month</li>
                <li>Light weight at less 130kg (Loading Stress of 2.8 kN/m<sup>2</sup>)</li>
                <li>Accept coins or bills</li>
                <li>3 hours of thermal holding power - Ice cream can be kept inside up to 3 hours after power cut off (assuming freezer was at -20&#8451; before power cut off</li>
                <li>Remote stock monitoring system (optional)</li>
            </ul>
        </div>
        </div>

        <div class="row" style="padding-top: 25px;">
            <h4>Interested? Contact us</h4>
            <div class="panel panel-default">
                <div class="panel-body">
                {!! Form::open(['action'=>'ClientController@directVendingInquiry']) !!}
                    {!! Honeypot::generate('my_name', 'my_time') !!}
                    <div class="col-md-12 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                            {!! Form::text('name', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
                            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                            {!! Form::text('contact', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                            {!! Form::email('email', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('note', 'Message (Optional)', ['class'=>'control-label']) !!}
                            {!! Form::textarea('note', null, ['class'=>'form-control', 'rows'=>'3']) !!}
                        </div>
                    </div>
                    {!! Form::submit('Submit', ['class'=> 'btn btn-lg btn-success', 'style'=>'margin: 15px 0px 0px 10px;']) !!}
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $('.select').select2();
</script>
@stop
