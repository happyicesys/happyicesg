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
            font-size: 10px;
            line-height: 1.2;
        }
        table{
            font-size: 10px;
            font-family: 'Times New Roman';
        }
        th{
            font-size: 10px;
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
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inline"><strong>Tel:</strong></div>
                                <div class="inline" style="padding-left: 20px">
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
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{Carbon\Carbon::today()->format('d M y')}}</span>
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
                                <strong>Date range: {{$delivery_from}} till {{\Carbon\Carbon::today()->min(\Carbon\Carbon::parse($delivery_to))->format('Y-m-d')}}</strong>
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

                                    <td class="col-xs-1 text-right">
                                        {{ $deal->avg_unit_cost }}
                                    </td>

                                    <td class="col-xs-2 text-right">
                                        {{ number_format($deal->total_cost, 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td colspan="5" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="text-right">
                                    <strong>{{ number_format($totals['total_costs'], 2) }}</strong>
                                </td>
                            </tr>

                            @if($issuebillprofile->gst)
                                @php
                                    $gst = $totals['total_costs'] - $totals['total_costs']/1.07;
                                    $subtotal = $totals['total_costs'] - $gst;
                                @endphp
                            <tr>
                                <td colspan="5" class="text-right">
                                    <strong>GST (7%)</strong>
                                </td>
                                <td class="text-right">
                                    {{ number_format($gst, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">
                                    <strong>Subtotal</strong>
                                </td>
                                <td class="text-right">
                                    {{ number_format($subtotal, 2) }}
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="5" class="text-right">
                                    <strong>Subtotal</strong>
                                </td>
                                <td class="text-right">
                                    {{ $totals['total_costs'] }}
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

                <div class="col-xs-12" style="padding-top: 10px">
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