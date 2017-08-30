@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

<div class="row" style="padding: 20px 0px 20px 0px;">
    <div class="col-md-9 col-md-offset-3 col-xs-12">
      <p>
        Dear {{$data['name']}} ({{$data['person']->cust_id}}), <br><br>
        Thanks for the purchase on HappyIce Door to Door. The following is your order {{ $data['transaction'] ? $data['transaction']->id : ''}} {{ $data['dtdtransaction'] ? $data['dtdtransaction']->id : ''}}. <br><br>
        <div style="font-family: 'Open Sans'; font-size: 15px;">
            <table style="border: 1px solid black; border-collapse:collapse;">
                <tr style="background-color: #D8BFD8;">
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">#</th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">Item</th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">Qty</th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">Amount($)</th>
                </tr>
                <?php $counter = 0; ?>
                @foreach($data['qtyArr'] as $index => $qty)
                    @if($qty != null and $qty != '' and $qty != 0)
                    <?php $counter ++ ?>
                    <tr>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">{{$counter}}</td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;">{{$data['captionArr'][$index]}}</td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">{{$qty}}</td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="right">{{number_format(($data['amountArr'][$index]), 2, '.', ',')}}</td>
                    </tr>
                    @endif
                @endforeach
                @if($data['delivery'] != 0)
                <tr>
                    <th colspan="3" align="center" style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Delivery Fee
                    </th>
                    <td style="border: 1px solid black; padding:5px 15px 5px 15px; font-weight: bold;" align="right">
                        {{number_format($data['delivery'], 2, '.', ',')}}
                    </td>
                </tr>
                @endif
                <tr>
                    <th colspan="3" align="center" style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Total
                    </th>
                    <td style="border: 1px solid black; padding:0 15px 0 15px; font-weight: bold;" align="right">
                        {{number_format($data['total'], 2, '.', ',')}}
                    </td>
                </tr>
            </table>
        </div>
        </p>
        <br>
        <p>
            <span style="font-weight:bold">Send to:</span><br>
            {{-- <span class="col-xs-12"> {{$person->block}}, #{{$person->floor}} - {{$person->unit}}</span> --}}
            <span class="col-xs-12">{{$data['block']}}, #{{$data['floor']}}-{{$data['unit']}}</span>
            <span class="col-xs-12">Singapore {{$data['postcode']}}</span> <br>
            <span style="font-weight:bold">Contact Number:</span>&nbsp;{{ $data['person']->contact }}<br>
            <span style="font-weight:bold">Preferred Timing:</span>&nbsp;{{ $data['timing'] }}<br>
            <span style="color:red;">**Cash payment upon delivery, We will contact you for final delivery timing via Phone/ SMS</span>
            <br><br>
            @if($data['remark'])
            <span style="font-weight:bold">Remark:</span>&nbsp;{{ $data['remark'] }} <br><br>
            @endif
            Thanks again and have a great day ahead.
        </p>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-xs-12 text-center" style="padding: 0px 0px 20px 0px;">
        <a href="/" class="btn btn-success">Return to Main Page</a>
    </div>
</div>

@php
    if(auth()->guest()) {
        request()->session()->flush();
    }
@endphp
@stop