@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<section id="services" style="padding-top: 30px;">
    <div class="container">
        <h2 class="text-center">Happy Ice Fun Vending Machine</h2>
        <img class="center-block" src="/img/vending/3D_Fun_Vending_Machine.png" class="img-responsive" alt="First Ice Cream Vending Machine">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">

                <p class="text-muted" style="font-size: 18px; padding-bottom: 20px;">Happy Ice designed its own vending machine - Ice Cream Fun-Vending machine that injects elements of fun into an otherwise boring vending process. We turn a traditional claw crane machine into a fully functional ice cream vending machine. The machine allows customers to buy the ice cream and play games at the same time. There are currently 20 over of this fun vending machines across the island.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-xs-12">
              <iframe width="750" height="500" src="https://www.youtube.com/embed/PbpaggVYLws" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="col-md-4 col-xs-12" style="font-size: 18px;">
                <ul style="padding: 20px 0px 0px 15px;">
                    <li>If you have played a toy claw machine before, you definitely know how to operate our fun vending machine.</li>
                    <li>However you just have to pay once with our machine, and unlimited chances will be given to you to catch the ice cream. Play till you get one!</li>
                    <li>If you are in good luck, you might get 2 ice creams in one catch.</li>
                    <li>Once the ice cream is dispensed, the game/vending process ends.</li>
                </ul>
            </div>
        </div>

        <div class="row" style="padding-top: 20px;">
            <div class="col-md-4 col-xs-12">
                <img src="/img/vending/Ice_cream_vending_machine_step1.png" class="img-responsive" alt="Ice Cream Vending Machine Step 1">
            </div>
            <div class="col-md-4 col-xs-12">
                <img src="/img/vending/Ice_cream_vending_machine_step2.png" class="img-responsive" alt="Ice Cream Vending Machine Step 2">
            </div>
            <div class="col-md-4 col-xs-12">
                <img src="/img/vending/Ice_cream_vending_machine_step3.png" class="img-responsive" alt="Ice Cream Vending Machine Step 3">
            </div>
        </div>

        <h2 style="color:#E54E91">Features</h2>
        <div class="col-md-10 col-xs-12" style="border: black solid 1px; background-color: #F49AC1; font-size: 20px; padding: 20px 0px 20px 0px;">
            <ul>
                <li>Infuse entertainment elements into a traditional vending machine</li>
                <li>Maximum ice cream holding capacity of 250 pieces</li>
                <li>Remote stock monitoring system</li>
                <li>Power cut off alarm system</li>
                <li>Low power consumption - Less than $30 per month</li>
                <li>Accept coins or bills (Bill acceptor is optional)</li>
            </ul>
        </div>

        <div class="col-md-9 col-xs-12" style="border: solid black 1px; margin-top: 10px;">
            <img src="/img/vending/Fun_Vending_Machine_UseCase.jpg" class="img-responsive" alt="Fun Vending Machine Use Case">
        </div>

        <div class="row">
            <div class="col-md-12 col-xs-12" style="margin-top: 10px;">
                <img src="/img/vending/TechnicalSpec.png" class="img-responsive" alt="Technical Specs">
            </div>
        </div>

        <h3>Interested in having this fun machine? Call us today or submit the following form.</h3>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
                {!! Form::open(['action'=>'ClientController@vendingInquiry']) !!}
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
                            {!! Form::email('email', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('loc', 'Proposed Vending Location', ['class'=>'control-label']) !!}
                            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                            {!! Form::select('loc',
                                $locArr,
                                null,
                                ['class'=>'select form-control'])
                            !!}
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
