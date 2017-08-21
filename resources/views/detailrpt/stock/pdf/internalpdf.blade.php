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
            font-family: "Helvetica";
            font-size: 12px;
            line-height: 1.2;
        }
        table{
            font-size: 12px;
            font-family: 'Times New Roman';
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


    </style>
    </head>

    <body>
        <div class="container">
            <div class="col-xs-10 col-xs-offset-1" style="font-size:15px">
                <h3 class="text-center"><strong>{{$issuebillprofile->name}}</strong></h3>
                <h5 class="text-center" style="margin-bottom: -5px">{{$issuebillprofile->address}}</h5>
                <h5 class="text-center" style="margin-bottom: -5px">Tel: {{$issuebillprofile->contact}}</h5>
                <h5 class="text-center">Co Reg No: {{$issuebillprofile->roc_no}}</h5>
            </div>

            <div class="col-xs-12" style="padding-top: 5px">
                <div class="row no-gutter">
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                            <div style="font-size:14px"><strong>Bill To:</strong></div>
                            <div style="border: solid thin; height:70px; padding-top:10px; padding-bottom: 10px;">
                                <span class="col-xs-12">
                                    <strong>{{$profile->name}}</strong>
                                </span>
                                <span class="col-xs-12">{{$profile->address}}</span>
                            </div>
                        </div>

                        <div style="padding-top:15px">
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
                                        <span class="inline">{{$issuebillprofile->payterm->name}}</span>
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
                <div class="col-xs-12" style="padding-top: 10px">
                    <table class="table table-bordered table-condensed" style="border:thin solid black;">
                        <tr>
                            <th class="col-xs-1 text-center">
                                Item Code
                            </th>
                            <th class="col-xs-7 text-center">
                                Description
                            </th>
                            <th class="col-xs-2 text-center">
                                Quantity
                            </th>
                            <th class="col-xs-1 text-center">
                                Unit
                            </th>
                            <th class="col-xs-1 text-center">
                                Price(S$)
                            </th>
                            <th class="col-xs-1 text-center">
                                Amount (S$)
                            </th>
                        </tr>

                        @php
                            $counter = 0;
                        @endphp

                        @unless(count($deals)>0)
                            <td class="text-center" colspan="8">No Records Found</td>
                        @else
                            @foreach($deals as $index => $deal)
                                @php
                                    $counter++;
                                @endphp
                            @if( $counter >= 16)
                            <tr style="page-break-inside: always">
                            @else
                            <tr>
                            @endif
                                <td class="col-xs-1 text-center">
                                    {{ $deal->product_id }}
                                </td>
                                <td class="col-xs-7">
                                    {{ $deal->item_name}} {{ $deal->item_remark }}
                                </td>

                                <td class="col-xs-2 text-right">
                                    {{ $deal->qty }}
                                </td>

                                <td class="col-xs-1 text-center">
                                    {{ $deal->unit }}
                                </td>

                                <td class="col-xs-1 text-right">
                                    {{ $deal->avg_unit_cost }}
                                </td>

                                <td class="col-xs-1 text-right">
                                    {{ $deal->total_cost }}
                                </td>
                            </tr>
                            @endforeach
{{--
                        @if($issuebillprofile->gst)
                        <tr class="noBorder">
                            <td colspan="4">
                                <span class="col-xs-offset-7" style="padding-left:33px;"><strong>SubTotal</strong></span>
                            </td>
                            <td class="text-right">
                                <strong>{{ number_format($totalprice, 2) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="col-xs-3 text-center">
                                <span style="padding-left:210px;"><strong>GST (7%)</strong></span>
                            </td>
                            <td class="text-right">
                                {{ number_format(($totalprice * 7/100), 2)}}
                            </td>
                        </tr>
                        @endif

                        @if($transaction->delivery_fee != 0)
                        <tr>
                            <td colspan="4" class="col-xs-3 text-center">
                                <span style="padding-left:210px;"><strong>Delivery Fee</strong></span>
                            </td>
                            <td class="text-right">
                                {{ number_format(($transaction->delivery_fee), 2)}}
                            </td>
                        </tr>
                        @endif

                        <tr>
                            @if($person->profile->gst)
                                <td colspan="3">
                                    <span class="col-xs-offset-8" style="padding-left:0px;"><strong>Total</strong></span>
                                    <span class="pull-right">{{$totalqty}}</span>
                                </td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{ $transaction->delivery_fee != 0 ? number_format(($totalprice * 107/100 + $transaction->delivery_fee), 2, '.', ',') : number_format(($totalprice * 107/100), 2) }}</strong>
                                </td>
                            @else
                                <td colspan="3">
                                    <span class="col-xs-offset-8" style="padding-left:0px;"><strong>Total</strong></span>
                                    <span class="pull-right">{{$totalqty}}</span>
                                </td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{ $transaction->delivery_fee != 0 ? number_format(($totalprice + $transaction->delivery_fee), 2) : $totalprice}}</strong>
                                </td>
                            @endif
                        </tr> --}}
                        @endunless
                    </table>
                </div>
            </div>
            </div>

        </div>

    </body>
</html>