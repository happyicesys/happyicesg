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
        .page-break {
            page-break-after: always;
            page-break-inside: avoid;
        }
        .avoid-break {
            page-break-inside: avoid;
        }

    </style>
    </head>


        <div class="container">
            <div class="col-xs-10 col-xs-offset-1" style="font-size:15px">
                <h3 class="text-center"><strong>{{$person->profile->name}}</strong></h3>
                <h5 class="text-center" style="margin-bottom: -5px">{{$person->profile->address}}</h5>
                <h5 class="text-center" style="margin-bottom: -5px">Tel: {{$person->profile->contact}}</h5>
                <h5 class="text-center">
                    @if($person->profile->gst)
                        Co. Reg & GST Reg No:
                    @else
                        Co. Reg No:
                    @endif
                    {{$person->profile->roc_no}}
                </h5>
            </div>

            <div class="row" style="padding-top: 5px">
                <div class="row no-gutter">
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                            <div style="font-size:14px"><strong>Bill To:</strong></div>
                            <div style="border: solid thin; height:120px; padding-bottom: 15px;">
                            <span class="col-xs-12"><strong>{{$person->company}}</strong></span>
                            <span class="col-xs-12">{{$person->bill_address}}</span>
                            </div>
                        </div>

                        <div style="padding-top:20px">
                            <div class="form-group" style="margin-bottom: 0px">
                                <div class="inline"><strong>Attn:</strong></div>
                                <div class="inline col-xs-offset-1">
                                    {{$person->name}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inline"><strong>Tel:</strong></div>
                                <div class="inline col-xs-offset-1">{{$person->contact}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-offset-4 col-xs-4">
                        <div class="form-group" style="padding-left:10px; margin-top:-5px;">
                            <div class="col-xs-12 row">
                                <div style="font-size: 140%;" class="text-center">
                                    <strong>Consolidated Tax Invoice</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Date From:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$delivery_from}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Date To:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$delivery_to}}</span>
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
                                        <span class="inline">{{$person->payterm}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Generate By:</strong></span>
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
    <body>
            <div class="avoid">
                <div class="row">
                    <table class="table table-condensed" style="font-size: 10px;">
                        <tr>
                            <th class="col-xs-1 text-center">
                                #
                            </th>
                            <th class="col-xs-1 text-center">
                                Inv #
                            </th>
                            <th class="col-xs-1 text-center">
                                Machine ID
                            </th>
                            <th class="col-xs-3 text-center">
                                Machine Name
                            </th>
                            <th class="col-xs-1 text-center">
                                Date
                            </th>
                            <th class="col-xs-1 text-center">
                                Amount
                            </th>
                        </tr>

                        @unless(count($transactions)>0)
                            <td class="text-center" colspan="14">No Records Found</td>
                        @else
                        @php
                            // dd(request()->all(),$transactions);
                        @endphp
                        @foreach($transactions as $index => $transaction)
                        <tr>
                            <td class="col-xs-1 text-center">
                                {{$index + 1}}
                            </td>
                            <td class="col-xs-1 text-center">
                                {{ $transaction->id }}
                            </td>
                            <td class="col-xs-1 text-center">
                                {{ $transaction->cust_id}}
                            </td>
                            <td class="col-xs-3 text-left">
                                {{ $transaction->company}}
                            </td>
                            <td class="col-xs-1 text-center">
                                {{$transaction->delivery_date ? \Carbon\Carbon::parse($transaction->delivery_date)->format('Y-m-d') : null}}
                            </td>
                            <td class="col-xs-1 text-right">
                                {{ $transaction->total}}
                            </td>
                        </tr>
                        @endforeach

                        @php
                            $subtotal = 0;
                            $gst = 0;
                            $total = 0;

                            if($person->profile->gst) {
                                $subtotal = number_format($totalprice - ($totalprice - $totalprice/(1 + $person->gst_rate/100)), 2);
                                $gst = number_format(($totalprice - $totalprice/(1 + $person->gst_rate/100)), 2);
                                $total = number_format($totalprice, 2);
/*                            }else if($person->profile->gst and !$person->is_gst_inclusive) {
                                $subtotal = number_format($totalprice, 2);
                                $gst = number_format($totalprice * $person->gst_rate/100, 2);
                                $total = number_format($totalprice + ($totalprice * $person->gst_rate/100), 2);*/
                            }else {
                                $total = number_format($totalprice, 2);
                            }
                        @endphp

                        @if($person->profile->gst)
                        <div class="avoid-break">
                            <tr class="noBorder">
                                <td colspan="2" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td colspan="2"></td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{$total}}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right">
                                    <strong>GST ({{$person->gst_rate + 0}}%)</strong>
                                </td>
                                <td colspan="2"></td>
                                <td></td>
                                <td class="text-right">
                                    {{$gst}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right">
                                    <strong>Exclude GST</strong>
                                </td>
                                <td colspan="2"></td>
                                <td></td>
                                <td class="text-right">
                                    {{$subtotal}}
                                </td>
                            </tr>
{{--                         @elseif($person->profile->gst and !$person->is_gst_inclusive)
                            <tr class="noBorder">
                                <td colspan="2" class="text-right">
                                    <strong>SubTotal</strong>
                                </td>
                                <td colspan="2"></td>
                                <td></td>
                                <td class="text-right">
                                    {{$subtotal}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right">
                                    <strong>GST ({{$person->gst_rate + 0}}%)</strong>
                                </td>
                                <td colspan="2"></td>
                                <td></td>
                                <td class="text-right">
                                    {{$gst}}
                                </td>
                            </tr>
                            <tr class="noBorder">
                                <td colspan="2" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-xs-2 text-right">
                                </td>
                                <td></td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{$total}}</strong>
                                </td>
                            </tr>--}}
                        @else
                            <tr class="noBorder">
                                <td colspan="2" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-xs-2 text-right">
                                </td>
                                <td></td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{$total}}</strong>
                                </td>
                            </tr>
                        @endif
                        @endunless
                    </div>
                    </table>
                </div>
            </div>

            <div class="col-xs-12">
                <div class="col-xs-12 avoid-break" style="padding-top: 20px">
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
                                <strong>{{$person->profile->name}}</strong>
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