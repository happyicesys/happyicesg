@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
<style>
    .title {
        font-size: 85px;
        margin: 0;
        display: table;
        font-weight: 100;
        font-family: 'Lato';
        padding-top: 100px;
        padding-bottom: 100px;
    }

    @media(min-width:1281px) {
        /*shift content to right and bottom*/
        .title{
            /* padding-left: 100px; */
        }
    }
</style>

<div class="title text-center">
    <img src="/img/Happy-Ice-Logo.png" alt="logo"  />
    <span class="col-xs-12">Thanks for your order</span>
    <p class="col-md-12 col-sm-12 col-xs-12" style="font-size: 25px;">
        Your order has been received and a confirmation email has been sent to you.
        Your order is only confirmed if you receive confirmation email.
        If you do not receive the confirmation email, do write to us
        <strong>onlineorder@happyice.com.sg</strong> to confirm your order.
    </p>
</div>
@stop
