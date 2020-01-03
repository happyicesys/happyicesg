@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
<style>
    .title {
        font-size: 60px;
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
    <span class="col-xs-12">Thank You!</span>
    @if($order_id)
    <span class="col-xs-12">Order ID: {{$order_id}}</span>
    @endif

    @php
        $pdf_url = "https://www.jotform.com/server.php?action=getSubmissionPDF&sid=".$submission_id."&formID=".$form_id;
        $edit_link = "https://www.jotform.com/edit/".$submission_id;
    @endphp

    @if($pdf_url)
    <span class="col-xs-12">
        Please
        <a class="btn btn-default" href={{$pdf_url}}>
            download pdf
        </a>
        for your reference.
    </span>
    @endif
    <p class="col-md-12 col-sm-12 col-xs-12" style="font-size: 25px;">
        Your order has been received and a confirmation email has been sent to you. Your order is only confirmed if you receive confirmation email. If you do not receive the confirmation email, do write to us
        <strong>onlineorder@happyice.com.sg</strong> or <strong>Whatsapp us at 98898718</strong> to confirm your order. to confirm your order.
    </p>
    @if($edit_link)
    <p class="col-md-12 col-sm-12 col-xs-12" style="font-size: 25px;">
        Click <a class="btn btn-default" href={{$edit_link}}>HERE</a> if you wish to modify your order. You are not allowed to modify your order online 24 hours before delivery timing.
    </p>
    @endif
    <div class="row">
    <a href="/next-day-delivery" class="btn btn-default">
        To Place Another Order, Click Here
    </a>
    </div>
</div>
@stop
