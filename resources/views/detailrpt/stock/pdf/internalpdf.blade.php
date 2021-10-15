<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    {{-- <link rel="stylesheet" href="../bootstrap-css/bootstrap.min.css"/>  --}}
    <style type="text/css" media="print">
        .inline {
            display:inline;
        }
        body{
            font-family: 'Helvetica';
            font-size: 13px;
            line-height: 1.2;
        }
        table{
            font-size: 13px;
            font-family: 'Verdana';
        }
        th{
            font-size: 12px;
        }
        footer{
            position: absolute;
            height: 210px;
            bottom: 5px;
            width: 100%;
        }
        html, body{
            height: 100%;
        }
        pre{
            font-size: 14px;
            font-family: 'Times New Roman';
            background-color: transparent;
        }
        tr {
            page-break-inside: avoid;
        }
        .page-break {
            page-break-after: always;
            page-break-inside: avoid;
        }
        .avoid-break {
            page-break-inside: avoid;
        }

    </style>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <span class="col-xs-12 text-center" style="font-size:14px">
                    <strong>{{$issuebillprofile->name}}</strong>
                </span>
                <span class="col-xs-12 text-center">
                    {{$issuebillprofile->address}}
                </span>
                <span class="col-xs-12 text-center">
                    Tel: {{$issuebillprofile->contact}}
                </span>
                <span class="col-xs-12 text-center">
                    Co Reg No: {{$issuebillprofile->roc_no}}
                </span>
            </div>

            <div class="col-xs-12" style="padding-top: 5px">
                <div class="row no-gutter">
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                            <div style="font-size: 10px">
                                @if($type === 'bill')
                                    <strong>Bill To:</strong>
                                @endif
                            </div>
                            <div style="border: solid thin; height:60px; padding-top:10px; padding-bottom: 5px;">
                                @if($type === 'bill')
                                    <span class="col-xs-12">
                                        <strong>{{$profile->name}}</strong>
                                    </span>
                                    <span class="col-xs-12">{{$profile->address}}</span>
                                @elseif($type === 'consolidate')
                                    <span class="col-xs-12">
                                        <strong>{{$issuebillprofile->name}}</strong>
                                    </span></span>
                                @endif
                            </div>
                        </div>

                        <div style="padding-top:5px">
                            <div class="form-group" style="margin-bottom: 0px">
                                <div class="inline"><strong>Attn:</strong></div>
                                <div class="inline col-xs-offset-1">
                                    {{$issuebillprofile->attn}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inline"><strong>Tel:</strong></div>
                                <div class="inline" style="padding-left: 20px">
                                    {{$issuebillprofile->contact}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-left:10px; margin-top:-5px;">
                            <div class="col-xs-12 row">
                                <div style="font-size: 130%;" class="text-center">
                                    @if($issuebillprofile->gst)
                                    <strong>DO/ TAX INVOICE</strong>
                                    @else
                                    <strong>DO/ INVOICE</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>DO/Inv No:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">
                                            <strong>{{$running_no}}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Date:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    @php
                                        $date = \Carbon\Carbon::parse($delivery_to)->format('d M y');
/*
                                        switch($type) {
                                            case 'bill':
                                                $date = \Carbon\Carbon::parse($delivery_to)->format('d M y');
                                                break;
                                            case 'consolidate':
                                                $date = \Carbon\Carbon::today()->format('d M y');
                                                break;
                                        }*/
                                    @endphp
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$date}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Term:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <small>
                                            <span class="inline">{{$issuebillprofile->payterm->name}}</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Prepare By:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{auth()->user()->name}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="avoid">
            <div class="row">
                <div class="col-xs-12" style="padding-top: 5px">
                    <table class="table table-bordered table-condensed" style="border:thin solid black;">
                        <tr>
                            <th class="col-xs-1 text-center">
                                Item Code
                            </th>
                            <th class="col-xs-7 text-center">
                                Description
                            </th>
                            <th class="col-xs-1 text-center">
                                Quantity
                            </th>
                            <th class="col-xs-1 text-center">
                                Unit
                            </th>
                            <th class="col-xs-1 text-center">
                                Price(S$)
                            </th>
                            <th class="col-xs-2 text-center">
                                Amount(S$)
                            </th>
                        </tr>

                        <tr>
                            <td class="text-center" colspan="12">
                                <strong>Date range: {{$delivery_from}} till {{$delivery_to}}</strong>
                            </td>
                        </tr>

                        @unless(count($deals)>0)
                            <tr>
                                <td class="text-center" colspan="12">No Records Found</td>
                            </tr>
                        @else
                            @foreach($deals as $index => $deal)
                                <tr>
                                    <td class="col-xs-1 text-center">
                                        {{ $deal->product_id }}
                                    </td>
                                    <td class="col-xs-7">
                                        {{ $deal->item_name}} {{ $deal->item_remark }}
                                    </td>

                                    <td class="col-xs-1 text-right">
                                        {{ number_format($deal->qty, 4) }}
                                    </td>

                                    <td class="col-xs-1 text-center">
                                        {{ $deal->unit }}
                                    </td>

                                    @php
                                        $avg_number = 0;
                                        $total_number = 0;

                                        switch($type) {
                                            case 'bill':
                                                $avg_number = $deal->avg_unit_cost;
                                                $total_number = $deal->total_cost;
                                                break;
                                            case 'consolidate':
                                                $avg_number = $deal->avg_sell_value;
                                                $total_number = $deal->amount;
                                                break;
                                        }
                                    @endphp
                                    <td class="col-xs-1 text-right">
                                        {{ number_format($avg_number, 2) }}
                                    </td>

                                    <td class="col-xs-2 text-right">
                                        {{ number_format($total_number, 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            @php
                                $totalvar = 0;
                                $subtotalvar = 0;
                                $gstvar = 0;

                                switch($type) {
                                    case 'bill':
                                        $totalvar = $totals['total_costs'];
                                        break;
                                    case 'consolidate':
                                        $totalvar = $totals['total_sell_value'];
                                        break;
                                }

                                if($issuebillprofile->gst) {
                                    if($profile->gst) {
                                        $gstvar = $totalvar * ($issuebillprofile->gst_rate/ 100);
                                        $subtotalvar = $totalvar;
                                        $totalvar = $subtotalvar + $gstvar;
                                    }else {
                                        $gstvar = $totalvar - $totalvar/ ((100 + $issuebillprofile->gst_rate)/ 100);
                                        $subtotalvar = $totalvar - $gstvar;
                                    }
                                }
                                $totalqty = $totals['total_qty'];
                            @endphp

                            @if($issuebillprofile->gst)
                                @if($profile->gst)
                                    <tr>
                                        <td colspan="2" class="text-left">
                                            <strong>Subtotal</strong>
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($totalqty, 4) }}
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-right">
                                            {{ number_format($subtotalvar, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">
                                            <strong>GST ({{number_format($issuebillprofile->gst_rate)}}%)</strong>
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($gstvar, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($totalvar, 2) }}</strong>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-left">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($totalqty, 4) }}
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-right">
                                            <strong>{{ number_format($totalvar, 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">
                                            <strong>GST ({{number_format($issuebillprofile->gst_rate)}}%)</strong>
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($gstvar, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">
                                            <strong>Exclude GST</strong>
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($subtotalvar, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @else
                                <tr>
                                    <td colspan="2" class="text-left">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($totalqty, 4) }}
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-right">
                                        {{ number_format($totalvar, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-left">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ number_format($totalvar, 2) }}</strong>
                                    </td>
                                </tr>
                            @endif
                        @endunless
                    </table>
                </div>
            </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label">Remarks:</label>
                    </div>
                </div>

                <div class="col-xs-12 avoid-break" style="padding-top: 10px">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <span class="text-center col-xs-12">
                                <strong>Goods Received in Good Conditions</strong>
                            </span>
                            <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:40px">
                                _______________________________
                            </span>
                            <span class="text-center col-xs-12" style="margin-top:0px">
                                <strong>Customer Sign & Co. Stamp</strong>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <span class="text-center col-xs-12">
                                <strong>{{$issuebillprofile->name}}</strong>
                            </span>
                            <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:40px">
                                _______________________________
                            </span>
                            <span class="text-center col-xs-12" style="margin-top:0px">
                                <strong>Payment Collected By</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </body>
</html>