@extends('template_client')
@section('title')
Door To Door
@stop
@section('content')

    <section id="portfolio" style="padding:60px 0px 0px 0px;">
        <div class="container-fluid">
            <div class="row no-gutter">
                <div class="col-md-6 col-xs-12">
                    <a href="#" class="portfolio-box">
                        <img src="img/d2d/a5 menu_final.jpg" class="img-responsive" alt="">
                        {{-- <div class="portfolio-box-caption"> --}}
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">

                                </div>
                            </div>
                        {{-- </div> --}}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <aside >
        <div class="container text-center">
            <div class="call-to-action">
                <h2>We Deliver to Your Doorstep too</h2>
                <a href="#" class="btn btn-primary btn-xl wow tada">Order Now!</a>
            </div>
        </div>
    </aside>

@stop