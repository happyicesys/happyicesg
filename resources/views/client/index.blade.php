@inject('item', 'App\Item')

@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
    <header>
        {{-- <div style="vertical-align: bottom;"> --}}
            {{-- <div class="header-content-inner"> --}}
                {{-- <span style="font-size: 70px; color: white;">Happy Ice, Healthier Life</span> --}}
                {{-- <hr> --}}
                {{-- <p style="color: black;">Oriental . Flavourful . Less Sweet</p> --}}
                <a href="/warehouse-sales" class="btn btn-default btn-xl" style="border: black solid 2px; margin-top: 350px;" >Find Out More</a>
            {{-- </div> --}}
        {{-- </div> --}}
    </header>

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
                        <img src="../img/portfolio/a1.jpg" class="img-responsive" alt="chocolate pie with mango">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">

                                </div>
                                <div class="project-name">
                                    {{-- {{$item::whereProductId('005')->first()->name}} --}}
                                    Chocolate Pie with Mango
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="/client/product" class="portfolio-box">
                        {{-- <img src="{{$item::whereProductId('004')->first()->main_imgpath}}" height="500" width="650" alt="{{$item::whereProductId('004')->first()->main_imgcaption}}"> --}}
                        <img src="../img/portfolio/a2.png" class="img-responsive" alt="Strawberry Frozen Yogurt">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    Strawberry Frozen Yogurt
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
                        <img src="../img/portfolio/a5.jpg" class="img-responsive" alt="OShare Mint Chocolate">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                </div>
                                <div class="project-name">
                                    OShare Mint Chocolate
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
                <a href="/brown-sugar-milk-boba-icecream" class="btn btn-primary btn-xl wow tada">Order Now!</a>
            </div>
        </div>
    </aside>

@stop