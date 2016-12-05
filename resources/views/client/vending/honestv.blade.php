@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<section id="services" style="padding-top: 15px;">
    <div class="container">
        <h2 class="text-center">Happy Ice HonestV Machine</h2>
        <img class="center-block" src="/img/vending/HonestV_3D.png" class="img-responsive" alt="HonestV Machine">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <h3>How Does HonestV Machine works?</h3>
                <p class="text-muted" style="font-size: 18px; padding-bottom: 20px;">This is a very simple and straightforward vending machine, where consumer put in exact coins/bills, and the machine door will be unlocked. The consumer would have to take out the ice cream from the fridge cabinet by themselves. Consumer take what they have paid for. The whole thing works because we trust in customer. By building on this trust, we are able to deploy this little vending machine at almost every office, be it small or big.</p>
            </div>
        </div>

        <h4>Call us today or fill up the following form to find out more about how this little HonestV can serve the needs of your office!</h4>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
                {!! Form::open(['action'=>'ClientController@honestVendingInquiry']) !!}
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
                            {!! Form::label('note', 'Note', ['class'=>'control-label']) !!}
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
