@inject('newsevents', 'App\NewsEvents')

@extends('template_client')
@section('title')
Ice Cream
@stop
@section('content')
    <div style="padding: 0px 10px 50px 10px; width: 100%;" class="col-md-12 text-center">
        {!! Html::image('img/happyice_b1.jpg', 'alt', array('class'=>'img-responsive')) !!}
    </div>
    <div class=" text-center" style="padding-bottom: 40px;">
        <span class="row" style="font-size: 36px;">
            Happy Ice, Healthier Life
        </span>
        <span class="row" style="font-size: 18px;">
            Over 12 ice cream products from Happy Ice are categorized as Healthier Snack by Singapore Health Promotion Board.
        </span>
        <span class="row" style="font-size: 16px;">
            <a href="#">View Our Products</a>
        </span>        
    </div>

    <div class="text-center jumbotron" style="font-size: 36px; padding-top: 50px; margin: 0px 10px 0px 10px;">
        <span>LATEST NEWS AND EVENTS</span> 
        <a href="https://www.facebook.com/happyice.com.sg/timeline"> 
        <i class="fa fa-facebook-official"></i> 
        </a>

        <div id="myCarousel" class="carousel slide" data-ride="carousel" style="margin-top: 50px;">
            <ol class="carousel-indicators">
                @foreach($newsevents::all() as $index => $newevent)
                    @if($index == 0)
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    @else
                        <li data-target="#myCarousel" data-slide-to="{{$index}}"></li>
                    @endif
                @endforeach
            </ol>            
        
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            @foreach($newsevents::all() as $index => $newevent)
                @if($index == 0)
                    <div class="item active">
                        <a href="{{$newevent->src}}">
                            <img class="img-responsive center-block" src="{{$newevent->src}}" alt="{{$newevent->alt}}" style="max-height: 350px;">
                        </a>
                    </div>
                @else
                    <div class="item">
                        <a href="{{$newevent->src}}">
                            <img class="img-responsive center-block" src="{{$newevent->src}}" alt="{{$newevent->alt}}" style="max-height: 350px;">
                        </a>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Left and right controls -->
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a> 
        </div>       
    </div>

    <div style="border: solid black thin;">
        {{-- <span style="font-size: 30px; padding: 10px 0px 15px 0px;" class="col-md-offset-5 col-md-7 ">Contact Us</span> --}}
        <span class="row col-md-offset-3 col-md-9" style="font-size: 28px; padding: 15px 0px 15px 0px;"><i class="fa fa-map-marker"></i> #04-125, TradeHub 21, 18, Boon Lay Way, S609966</span>
        <span class="row col-md-offset-3 col-md-9" style="font-size: 28px; padding: 0px 0px 15px 0px;"><i class="fa fa-phone-square"></i> +65-9777 3533 or +65-8500 9838</span>
        <span class="row col-md-offset-3 col-md-9" style="font-size: 28px; padding: 0px 0px 35px 0px;"><i class="fa fa-envelope"></i> daniel.ma@happyice.com.sg</span>
    </div>
@stop