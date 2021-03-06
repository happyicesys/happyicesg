@inject('item', 'App\Item')

@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
    {{-- <header> --}}

<style>
    .img-wrapper {
    position: relative;
    }

    .img-responsive {
    width: 100%;
    height: auto;
    }

    .img-overlay {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    }

    .img-overlay:before {
    content: ' ';
    display: block;
    /* adjust 'height' to position overlay content vertically */
    height: 50%;
    }
</style>

    {{-- <header> --}}
        <div class="img-wrapper">
            <img src="../img/mainslide.jpg" alt="ice cream online and offline order" class="img-responsive">
            <div class="img-overlay">
                <a href="/delivery" class="btn btn-danger btn-xl text-center">
                    Online Order Now
                </a>
            </div>
        </div>

    {{-- </header> --}}
    {{-- </header> --}}

    <section class="bg-primary" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">About Us</h2>
                    <hr class="light">
                    <p class="text-faded">Our aim is to provide more variety & healthy desserts. Most of our products have obtained accreditation as Healthier Choice products issued by Singapore Health Promotion Board.</p>
                    <a href="/client/about" class="btn btn-default btn-xl">More</a>
                </div>
            </div>
        </div>
    </section>

    <section id="services">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">Why Choose Us</h2>
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
                        <i class="fa fa-4x fa-paper-plane wow bounceIn text-primary" data-wow-delay=".1s"></i>
                        <h3>Anyway, Anytime, Happy Ice</h3>
                        <p class="text-muted">Grab the tasty healthier dessert anyway and anytime. We strike to build a comprehensive sales channel as close as possible to you. One would definitely attracted by our fun and eye-catchy vending machines; fast delivery speed through online purchase, and conveniently purchase from stores.</p>
                    </div>
                </div>
{{--                 <div class="col-lg-3 col-md-6 text-center">
                    <div class="service-box">
                        <i class="fa fa-4x fa-newspaper-o wow bounceIn text-primary" data-wow-delay=".2s"></i>
                        <h3>Lorem Ipsum</h3>
                        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="service-box">
                        <i class="fa fa-4x fa-heart wow bounceIn text-primary" data-wow-delay=".3s"></i>
                        <h3>Lorem Ipsum</h3>
                        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

    <section class="no-padding col-lg-12 col-md-12" id="portfolio">
        <div class="container-fluid" style="padding-bottom: 85px;">
            <div class="row no-gutter">
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        {{-- <img src="{{$item::whereProductId('005')->first()->main_imgpath}}" height="500" width="650" alt="{{$item::whereProductId('005')->first()->main_imgcaption}}"> --}}
                        <img src="../img/portfolio/a1.png" class="img-responsive center-block" alt="Brown Sugar Boba Ice Cream">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">

                                </div>
                                <div class="project-name">
                                    Brown Sugar Boba Ice Cream
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        {{-- <img src="{{$item::whereProductId('004')->first()->main_imgpath}}" height="500" width="650" alt="{{$item::whereProductId('004')->first()->main_imgcaption}}"> --}}
                        <img src="../img/portfolio/a2.png" class="img-responsive center-block" alt="Milk Ice Bar">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    Milk Ice Bar
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        {{-- <img src="{{$item::whereProductId('026')->first()->main_imgpath}}" height="500" width="650" alt="{{$item::whereProductId('026')->first()->main_imgcaption}}"> --}}
                        <img src="../img/portfolio/a3.jpg" class="img-responsive" alt="Vanilla Roll">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    Vanilla Roll
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        <img src="../img/portfolio/a4.jpg" class="img-responsive" alt="Chocolate Roll">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    Chocolate Roll
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        <img src="../img/portfolio/a5.jpg" class="img-responsive center-block" alt="Green Mango and Lemon">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    Green Mango and Lemon
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        <img src="../img/portfolio/a6.jpg" class="img-responsive" alt="Red Bean Jelly">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    Red Bean Jelly
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <aside>
        <div class="container text-center" style="padding-top: 30px;">
            <div class="call-to-action">
                <h2>We Deliver to Your Doorstep too</h2>
                <a href="/delivery" class="btn btn-primary btn-xl wow tada">Order Now!</a>
            </div>
        </div>
    </aside>

    <aside>
        <div class="container text-center" style="padding-top: 30px;">
            <div class="call-to-action">
                <h2>Also Featuring...</h2>
                <a href="https://www.foodline.sg/">
                    <img src="../img/featuring/foodline.png" class="img-responsive center-block" alt="Foodline SG">
                </a>
            </div>
        </div>
    </aside>

@stop