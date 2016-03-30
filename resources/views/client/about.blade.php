@inject('newsevents', 'App\NewsEvents')

@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<section id="services">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 text-center">
                <h2 class="section-heading">About Us</h2>
                <hr class="primary">
            </div>
            <div class="col-lg-6 col-md-6 text-center">
                <h2 class="section-heading">News and Events</h2>
                <hr class="primary">
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 text-center">
                <div class="service-box">
                    <i class="fa fa-4x fa-smile-o wow bounceIn text-primary"></i>
                    <h3>Happy Ice, Healthier Life</h3>
                    <p class="text-muted">Stay happy when you eat healthy. Happy Ice is committed to promote healthy dietary. Most of our products all are recognized with lesser sugar, fat and calories compared to similar products in the market</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 text-center">
                <div class="service-box">
                {{-- slideshow start --}}

                <div class="row">
                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
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
                                            <img class="img-responsive center-block" src="../../{{$newevent->src}}" alt="{{$newevent->alt}}" style="max-height: 350px;">
                                        </a>
                                    </div>
                                @else
                                    <div class="item">
                                        <a href="{{$newevent->src}}">
                                            <img class="img-responsive center-block" src="../../{{$newevent->src}}" alt="{{$newevent->alt}}" style="max-height: 350px;">
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
                {{-- slideshow end --}}

                </div>
            </div>

        </div>
    </div>
</section>
@stop

@section('footer')
    <script src="http://pupunzi.com/mb.components/mb.YTPlayer/demo/inc/jquery.mb.YTPlayer.js"></script>
    <script>
        $(".player").mb_YTPlayer();
    </script>
@stop