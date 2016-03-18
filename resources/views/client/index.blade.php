@inject('newsevents', 'App\NewsEvents')

@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<div >

    <div class="wrapper">
       <img src="img/main1.jpg" class="img-back img-responsive" alt="Responsive image">
{{--         <div class="sol-sm-6">
        <h1 id="text">Hello World!</h1>
        </div> --}}
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/3-vanilla.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/4-chocroll.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/5-vanillaroll.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/10-gmanlemon.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/11-redbeantaro.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/12-tarochunk.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/13-redbeanjelly.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/14-qqpudding.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/15-chocmango.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/16-chocpeanut.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/17-chocvanilla.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/19-matcha.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/20-pineappleguava.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center" style="padding-top:30px;">
            <h1>And</h1>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="wrapper">
           <img src="img/vending_title.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

<!--Video Section-->
<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1 thick-border" style="margin-top:30px; padding:0px 0px 0px 0px;">
<section class="content-section video-section">
  <div class="pattern-overlay">
  <a id="bgndVideo" class="player" data-property="{videoURL:'https://youtu.be/MTMmVnkKls8',containment:'.video-section', quality:'large', autoPlay:true, mute:true, opacity:1}">bg</a>
{{--     <div class="container">
      <div class="row">
        <div class="col-lg-12">
        <h1>Full Width Video</h1>
        <h3>Enjoy Adding Full Screen Videos to your Page Sections</h3>
       </div>
      </div>
    </div> --}}
  </div>
</section>
</div>
<!--Video Section Ends Here-->

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/vending2.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="wrapper">
           <img src="img/vending1.jpg" class="img-back img-responsive" alt="Responsive image">
        </div>
    </div>

    <div class="row" style="margin-top: 50px;">
        <span class="row col-md-offset-3 col-md-9" style="font-size: 28px; padding: 15px 0px 15px 0px;"><i class="fa fa-map-marker"></i> #04-125, TradeHub 21, 18, Boon Lay Way, S609966</span>
        <span class="row col-md-offset-3 col-md-9" style="font-size: 28px; padding: 0px 0px 15px 0px;"><i class="fa fa-phone-square"></i> +65-9777 3533 or +65-8500 9838</span>
        <span class="row col-md-offset-3 col-md-9" style="font-size: 28px; padding: 0px 0px 35px 0px;"><i class="fa fa-envelope"></i> daniel.ma@happyice.com.sg</span>
    </div>

{{--
    <div class="col-md-12 bgimage" style="height: 1790px;">
        <div class="bgimage-inside">
        </div>
    </div>
--}}
{{--
    <div class=" text-center" style="padding-bottom: 40px;">
        <span class="row" style="font-size: 18px;">
            Over 12 ice cream products from Happy Ice are categorized as Healthier Snack by Singapore Health Promotion Board.
        </span>
    </div>

    <div ng-app="app" ng-controller="clientMainController">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12" style="margin: 0px 20px 0px 0px;">
                <div dir-paginate="product in products | itemsPerPage:itemsPerPage"  current-page="currentPage" class="col-md-4 col-sm-4 col-xs-4" style="font-size:20px;">
                    <img class="img-responsive img-center" ng-src="@{{product.main_imgpath}}" ng-alt="@{{product.main_imgcaption}}" style="max-height: 350px;">
                    <p class="product-name text-center">@{{product.name}}</p>
                </div>

            </div>
        </div>

        <div class="row text-center" style="padding: 30px 0px 100px 0px; color:blue;">
            <div class="col-md-12 col-sm-12 col-xs-12" style="font-size: 16px;">
                <button ng-click="showAllProduct()" class="btn btn-primary thick-border" style="padding: 15px 50px 15px 50px; font-size:18px;" ng-model="productModel">@{{productText}}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="text-center jumbotron" style="font-size: 36px; padding-top: 50px; margin: 0px 0px 0px 0px;">
            <span>Latest News and Events</span>
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
    </div> --}}

</div>

<script src="/js/client_index.js"></script>
<script src="http://pupunzi.com/mb.components/mb.YTPlayer/demo/inc/jquery.mb.YTPlayer.js"></script>
<script>
    $(".player").mb_YTPlayer();
</script>
@stop