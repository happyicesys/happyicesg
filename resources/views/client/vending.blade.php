@inject('newsevents', 'App\NewsEvents')

@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<section id="services" style="padding-top: 30px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h3>Happy Ice Vending Machine</h3>
                <p class="text-muted">Happy Ice designed its own vending machine - Ice Cream Fun-Vending machine that injects elements of fun into an otherwise boring vending process. We turn a traditional claw crane machine into a fully functional ice cream vending machine. The machine allows customers to buy the ice cream and play games at the same time. There are currently 20 over of this fun vending machines across the island.</p>
            </div>
        </div>
{{--
        <section class="content-section video-section">
            <div class="row">
            <div class="col-md-12 col-xs-12 col-lg-12">
            <div id="p1" class="player" data-property="{videoURL:'https://youtu.be/PbpaggVYLws',containment:'.video-section', quality:'large', autoPlay:true, mute:true, opacity:1}"></div>
            </div>
            </div>
        </section> --}}
        <video id="vending" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="900" height="500" data-setup='{"example_option":true}'>
            <source src="https://youtu.be/PbpaggVYLws" type="video/webm">
            Your browser does not support the video tag.
        </video>

        <div class="row">
            <div class="col-lg-12 col-md-12" style="font-size: 16px;">
                <ul style="padding: 20px 0px 0px 15px;">
                    <li>If you have played a toy claw machine before, you definitely know how to operate our fun vending machine.</li>
                    <li>However you just have to pay once with our machine, and unlimited chances will be given to you to catch the ice cream. Play till you get one!</li>
                    <li>If you are in good luck, you might get 2 ice creams in one catch.</li>
                    <li>Once the ice cream is dispensed, the game/vending process ends.</li>
                </ul>
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