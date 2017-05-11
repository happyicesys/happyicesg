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
            font-size: 12px;
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
                <h3 class="text-center"><strong>{{$person->profile->name}}</strong></h3>
                <h5 class="text-center" style="margin-bottom: -5px">{{$person->profile->address}}</h5>
                <h5 class="text-center" style="margin-bottom: -5px">Tel: {{$person->profile->contact}}</h5>
                <h5 class="text-center">Co Reg No: {{$person->profile->roc_no}}</h5>
            </div>

            <div class="col-xs-12" style="padding-top: 5px">
                <div class="row no-gutter">
                    <div class="col-xs-4">
                        @if($person->cust_id[0] == 'H')
                            <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                                <div style="font-size:14px"><strong>Send To:</strong></div>
                                <div style="border: solid thin; height:120px; padding-bottom: 15px;">
                                {{-- <span class="col-xs-12"> {{$person->block}}, #{{$person->floor}} - {{$person->unit}}</span> --}}
                                <span class="col-xs-12">{{$transaction->del_address ? $transaction->del_address : $person->del_address}}</span>
                                <span class="col-xs-offset-1">{{$transaction->del_postcode ? $transaction->del_postcode : $person->del_postcode}}</span>
                                </div>
                            </div>
                        @else
                            <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                                <div style="font-size:14px"><strong>Bill To:</strong></div>
                                <div style="border: solid thin; height:120px; padding-bottom: 15px;">
                                <span class="col-xs-12"> {{$person->cust_id}}, {{$person->com_remark}}</span>
                                <span class="col-xs-12">{{$person->company}}</span>
                                <span class="col-xs-12">{{$transaction->bill_address ? $transaction->bill_address : $person->bill_address}}</span>
                                {{-- <span class="col-xs-offset-1">{{$person->bill_postcode}}</span> --}}
                                </div>
                            </div>
                        @endif

                        <div style="padding-top:20px">
                            <div class="form-group" style="margin-bottom: 0px">
                                <div class="inline"><strong>Attn:</strong></div>
                                <div class="inline col-xs-offset-1">
                                    @if($person->cust_id[0] == 'H')
                                        {{$person->cust_id}} - {{$transaction->name ? $transaction->name : $person->name}}
                                    @else
                                        {{$transaction->name ? $transaction->name : $person->name}}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inline"><strong>Tel:</strong></div>
                                <div class="inline" style="padding-left: 20px">{{$transaction->contact ? $transaction->contact : $person->contact}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        @unless($person->cust_id[0] == 'H')
                            <div class="form-group" style="padding: 3px 0px 0px 10px">
                                <div style="font-size:14px"><strong>Send To:</strong></div>
                                <div style="border: solid thin; height:120px; padding-bottom: 15px;">
                                    @if($person->site_name)
                                        <span class="col-xs-12">{{$person->site_name}}</span>
                                    @endif

                                    <span class="col-xs-12">{{$transaction->del_address ? $transaction->del_address : $person->del_address}}</span>

                                    <span class="col-xs-offset-1">{{$transaction->del_postcode ? $transaction->del_postcode : $person->del_postcode}}</span>
                                </div>
                            </div>
                        @endunless
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-left:10px; margin-top:-5px;">
                            <div class="col-xs-12 row">
                                <div style="font-size: 130%;" class="text-center">
                                    @if($person->profile->gst)
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
                                        <span class="inline">{{$transaction->id}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Order On:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{Carbon\Carbon::createFromFormat('Y-m-d', $transaction->order_date)->format('d M y')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>Delivery On:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{Carbon\Carbon::createFromFormat('Y-m-d', $transaction->delivery_date)->format('d M y')}}</span>
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
                                        <span class="inline" style="font-size: 85%;"><strong>Updated By:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$transaction->updated_by}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>PO#:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$transaction->po_no}}</span>
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
                                Price/Unit (S$)
                            </th>
                            <th class="col-xs-1 text-center">
                                Amount (S$)
                            </th>
                        </tr>

                        <?php $counter = 0; ?>
                        @unless(count($deals)>0)
                        <td class="text-center" colspan="8">No Records Found</td>
                        @else
                        @foreach($deals as $index => $deal)
                        <?php $counter ++ ?>
                        @if( $counter >= 16)
                        <tr style="page-break-inside: always">
                        @else
                        <tr>
                        @endif
                            <td class="col-xs-1 text-center">
                                {{ $deal->item->product_id }}
                            </td>
                            <td class="col-xs-7">
                                {{ $deal->item->name}} {{ $deal->item->remark }}
                            </td>

                            @if($deal->divisor and $deal->item->is_inventory === 1)
                                <td class="col-xs-2 text-right">
                                    {{ $deal->divisor == 1 ? $deal->qty + 0 : $deal->dividend.'/'.$deal->divisor}} {{ $deal->item->unit }}
                                </td>
                            @elseif($deal->item->is_inventory === 0)
                                <td class="col-xs-2 text-left">
                                    @if($deal->dividend === 1)
                                        1 Unit
                                    @else
                                        {{$deal->dividend}} Unit
                                    @endif
                                </td>
                            @else
                                <td class="col-xs-2 text-right">
                                    {{ $deal->qty + 0 }}
                                </td>
                            @endif

                            @if($deal->unit_price == 0 || $deal->unit_price == null)
                            <td class="col-xs-1 text-right">
                                {{ number_format(($deal->amount / $deal->qty), 2, '.', ',')}}
                            </td>
                            @else
                            <td class="col-xs-1 text-right">
                                {{ $deal->unit_price }}
                            </td>
                            @endif
                            @if($deal->amount != 0)
                            <td class="col-xs-1 text-right">
                                {{ $deal->amount }}
                            </td>
                            @else
                            <td class="col-xs-1 text-right">
                                <strong>FOC</strong>
                            </td>
                            @endif
                        </tr>
                        @endforeach

                        @if($person->profile->gst)
                        <tr class="noBorder">
                            <td colspan="4">
                                <span class="col-xs-offset-7" style="padding-left:33px;"><strong>SubTotal</strong></span>
                            </td>
                            <td class="text-right">
                                <strong>{{ $totalprice }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="col-xs-3 text-center">
                                <span style="padding-left:210px;"><strong>GST (7%)</strong></span>
                            </td>
                            <td class="text-right">
                                {{ number_format(($totalprice * 7/100), 2, '.', ',')}}
                            </td>
                        </tr>
                        @endif

                        @if($transaction->delivery_fee != 0)
                        <tr>
                            <td colspan="4" class="col-xs-3 text-center">
                                <span style="padding-left:210px;"><strong>Delivery Fee</strong></span>
                            </td>
                            <td class="text-right">
                                {{ number_format(($transaction->delivery_fee), 2, '.', ',')}}
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
                                    <strong>{{ $transaction->delivery_fee != 0 ? number_format(($totalprice * 107/100 + $transaction->delivery_fee), 2, '.', ',') : number_format(($totalprice * 107/100), 2, '.', ',') }}</strong>
                                </td>
                            @else
                                <td colspan="3">
                                    <span class="col-xs-offset-8" style="padding-left:0px;"><strong>Total</strong></span>
                                    <span class="pull-right">{{$totalqty}}</span>
                                </td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{ $transaction->delivery_fee != 0 ? number_format(($totalprice + $transaction->delivery_fee), 2, '.', ',') : $totalprice}}</strong>
                                </td>
                            @endif
                        </tr>
                        @endunless
                    </table>
                </div>
            </div>
            </div>

        {{-- <footer class="footer"> --}}
                <div class="col-xs-12">
                    <div class="col-xs-12">
                        @unless($person->cust_id[0] == 'H' or $person->cust_id[0] == 'D')
                            Payment by cheque should be crossed and made payable to "{{$person->profile->name}}"
                        @endunless
                    </div>
                    <div class="col-xs-12" style="padding-top:10px">
                        <div class="form-group">
                            @if($transaction->transremark)
                                <label class="control-label">Remarks:</label>
                                <pre>{{ $transaction->transremark }}</pre>
                            @endif
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
        {{-- </footer>             --}}
        </div>

    </body>
</html>