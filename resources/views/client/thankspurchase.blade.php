@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')
<style>
    .title {
        font-size: 40px;
        margin: 0;
        display: table;
        font-weight: 100;
        font-family: 'Verdana';
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
    <span class="col-xs-12">Thank You for your Order</span>
    @if($order_id)
    <span class="col-xs-12">Your Order ID: {{$order_id}}</span>
    @endif

    @php
        $pdf_url = '';
        $edit_link = '';

        if($submission_id and $form_id) {
            $pdf_url = "https://www.jotform.com/server.php?action=getSubmissionPDF&sid=".$submission_id."&formID=".$form_id;

            $edit_link = "https://www.jotform.com/edit/".$submission_id;
        }

    @endphp

    @if($pdf_url)
    <span class="col-xs-12">
        Please
        <a class="btn btn-lg btn-primary" href={{$pdf_url}}>
            download pdf
        </a>
        for your reference.
    </span>
    @endif
    <p class="col-md-offset-1 col-md-10 col-sm-offset-1 col-sm-10 col-xs-12" style="font-size: 20px;">
        Your order has been received and a confirmation email has been sent to you.
        <br>
        If you do not receive the confirmation email, do write to us at
        <strong>onlineorder@happyice.com.sg</strong> or <strong>Whatsapp us at 98898718</strong> to confirm your order.
    </p>
    @if($edit_link)
    <p class="col-md-12 col-sm-12 col-xs-12" style="font-size: 20px;">
        Click <a class="btn btn-primary" href={{$edit_link}}>Here</a> if you wish to modify your order. You are allowed to modify your order within 30 mins after your order submission.
    </p>
    @endif
    <div class="row">
    <a href="/delivery" class="btn btn-primary">
    {{-- <a href="http://form.jotform.me/kentzo/{{$form_id}}" class="btn btn-primary"> --}}
        To Place Another Order, Click Here
    </a>
    </div>
</div>
@stop
