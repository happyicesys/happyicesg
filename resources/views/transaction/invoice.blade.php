<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    {{-- <link rel="stylesheet" href="../bootstrap-css/bootstrap.min.css"/> --}}
    <style type="text/css" media="print">
        .inline {
            display:inline;
        }
        body{
            font-family: 'Helvetica';
            font-size: 15px;
        }
        table{
            font-size: 15px;
            font-family: 'Verdana';
        }
        label {
            font-size: 12px;
        }
        th{
            font-size: 13px;
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
            font-size: 15px;
            font-family: 'Times New Roman';
            background-color: transparent;
        }
        thead{
        display: table-header-group;
        }
        tfoot {
        display: table-row-group;
        }
        tr {
        page-break-inside: avoid;
        }
		.page-break {
		    page-break-after: always;
		}

    </style>
    </head>

    <body>
        <div class="container">
            <div class="col-xs-10 col-xs-offset-1" style="font-size:15px">
                <h3 class="text-center"><strong>{{$person->profile->name}}</strong></h3>
                <h5 class="text-center" style="margin-bottom: -5px">{{$person->profile->address}}</h5>
                <h5 class="text-center" style="margin-bottom: -5px">Tel: {{$person->profile->contact}}</h5>
                <h5 class="text-center">
                    @if($transaction->gst)
                        Co. Reg & GST Reg No:
                    @else
                        Co. Reg No:
                    @endif
                    {{$person->profile->roc_no}}
                </h5>
            </div>

            <div class="row" style="padding-top: 5px">
                <div class="row no-gutter">
                    <div class="col-xs-8">
                        <div class="col-xs-12">
                            <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                                <div style="font-size:14px"><strong>Customer:</strong></div>
                                <div style="border: solid thin; height:40px; padding-bottom: 10px;">
                                    <span class="col-xs-12">{{$person->code}} ({{$person->custPrefix->code}})</span>
                                    <span class="col-xs-12">{{$person->company}}</span>
                                    @if(($transaction->person->is_vending === 1 or $transaction->person->is_dvm) and $transaction->person->vending)
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <small>
                                            (
                                            Binded VM:  {{$transaction->person->vending->serial_no}}
                                            @if($transaction->person->vending->type)
                                            - {{$transaction->person->vending->type}}
                                            @endif
                                            )
                                            </small>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="col-xs-6">
                            @if($person->cust_id[0] == 'H')
                                <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                                    <div style="font-size:14px"><strong>Send To:</strong></div>
                                    <div style="border: solid thin; height:125px; padding-bottom: 15px;">
                                    {{-- <span class="col-xs-12"> {{$person->block}}, #{{$person->floor}} - {{$person->unit}}</span> --}}
                                    <span class="col-xs-12">{{$transaction->del_address ? $transaction->del_address : $person->del_address}}</span>
                                    <span class="col-xs-12">{{$transaction->deliveryCountry ? $transaction->deliveryCountry->name : $person->deliveryCountry->name}} {{$transaction->del_postcode ? $transaction->del_postcode : $person->del_postcode}}</span>
                                    </div>
                                </div>
                            @else
                                <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                                    <div style="font-size:14px"><strong>Bill To:</strong></div>
                                    <div style="border: solid thin; height:125px; padding-bottom: 15px;">
    {{--                                 @if($person->franchisee)
                                        <span class="col-xs-12"> {{$person->franchisee->company_name}}</span>
                                        <span class="col-xs-12">{{$person->franchisee->bill_address}}</span>
                                    @else --}}
                                        <span class="col-xs-12">{{$person->com_remark}}</span>
                                        <span class="col-xs-12">{{$transaction->bill_address ? $transaction->bill_address : $person->bill_address}}</span>
                                        <span class="col-xs-12">{{$transaction->billingCountry ? $transaction->billingCountry->name : $person->billingCountry->name}} {{$transaction->bill_postcode ? $transaction->bill_postcode : $person->bill_postcode}}</span>
                                    {{-- @endif --}}
                                    </div>
                                </div>
                            @endif

                            @if(!$transaction->is_deliveryorder)
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
                            @endif
                        </div>
                        <div class="col-xs-6">
                            @unless($person->cust_id[0] == 'H' or $transaction->is_deliveryorder)
    {{--                             @if($person->franchisee)
                                <div class="form-group" style="padding: 3px 0px 0px 10px">
                                    <div style="font-size:14px"><strong>Send To:</strong></div>
                                    <div style="border: solid thin; height:120px; padding-bottom: 15px;">
                                        @if($person->site_name)
                                            <span class="col-xs-12"> {{$person->cust_id}}</span>
                                            <span class="col-xs-12">{{$person->site_name}}</span>
                                            <span class="col-xs-12">{{$person->com_remark}}</span>
                                        @endif
                                    </div>
                                </div>
                                @else --}}
                                <div class="form-group" style="padding: 3px 0px 0px 10px">
                                    <div style="font-size:14px"><strong>Send To:</strong></div>
                                    <div style="border: solid thin; height:125px; padding-bottom: 15px;">

                                        @if($person->site_name)
                                            <span class="col-xs-12">{{$person->site_name}}</span>
                                        @endif

                                        <span class="col-xs-12">{{$transaction->del_address ? $transaction->del_address : $person->del_address}}</span>

                                        <span class="col-xs-12">{{$transaction->deliveryCountry ? $transaction->deliveryCountry->name : $person->deliveryCountry->name}} {{$transaction->del_postcode ? $transaction->del_postcode : $person->del_postcode}}</span>
                                    </div>
                                </div>
                                {{-- @endif --}}
                            @endunless
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-left:10px; margin-top:-5px;">
                            <div class="col-xs-12 row">
                                <div style="font-size: 130%;" class="text-center">
                                    @if($transaction->is_service)
                                        <strong>
                                            Service
                                        </strong>
                                    @else
                                        @if($transaction->is_vending_generate)
                                            <strong>SALES REPORT</strong>
                                        @elseif($type == 'do')
                                            <strong>DELIVERY ORDER</strong>
                                        @elseif($type == 'quotation')
                                            <strong>QUOTATION</strong>
                                        @else
                                            @if($transaction->gst)
                                            <strong>DO/ TAX INVOICE</strong>
                                            @else
                                            <strong>DO/ INVOICE</strong>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5" style="font-size: 110%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline"><strong>DO/Inv#:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-7" style="font-size: 130%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <strong>
                                            <span class="inline">{{$inv_id}}</span>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5" style="font-size: 85%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline"><strong>PO#:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-7" style="font-size: 120%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <strong>
                                            <span class="inline">{{$transaction->po_no}}</span>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            @if(!$transaction->is_deliveryorder)
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 85%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Order Date:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 120%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{$transaction->order_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $transaction->order_date)->format('d M y') : ''}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 85%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Delivery Date:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 120%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{$transaction->delivery_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $transaction->delivery_date)->format('d M y') : ''}}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 85%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Submit On:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 120%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{$transaction->deliveryorder ? $transaction->deliveryorder->submission_datetime->format('d M y') : ''}}</span>
                                            <br>
                                            <span class="inline">{{$transaction->deliveryorder ? $transaction->deliveryorder->submission_datetime->format('H:m A') : ''}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-xs-5" style="font-size: 85%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline"><strong>Term:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-7" style="font-size: 120%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$person->payterm}}</span>
                                    </div>
                                </div>
                            </div>
                            @if(!$transaction->is_deliveryorder)
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 85%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Created By:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 120%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{$transaction->creator ? $transaction->creator->name : ''}}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 85%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Request By:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 120%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{\App\User::find($transaction->deliveryorder->requester)->name}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($transaction->driver)
                            <div class="row">
                                <div class="col-xs-5" style="font-size: 85%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" ><strong>Delivered By:</strong></span>
                                    </div>
                                </div>
                                <div class="col-xs-7" style="font-size: 120%;">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <strong>
                                        <span class="inline">
                                            {{$transaction->driver}}
                                            @if($transaction->sequence)
                                                ({{$transaction->sequence * 1}})
                                            @endif
                                        </span>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($transaction->is_deliveryorder)
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 110%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Job Type:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 120%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{$transaction->deliveryorder->job_type}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($transaction->person->vend_code)
                                <div class="row">
                                    <div class="col-xs-5" style="font-size: 110%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline"><strong>Vend#:</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-7" style="font-size: 110%;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <span class="inline">{{$transaction->person->vend_code}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($transaction->is_deliveryorder)
            <div class="row page-break">
                <div style="width: 100%; height: 35px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
                <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                    Pick-up Detail
                </span>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th class="col-xs-1 text-center">
                                    Pickup Date
                                </th>
                                <th class="col-xs-1 text-center">
                                    Time Range
                                </th>
                                <th class="col-xs-1 text-center">
                                    Contact Person
                                </th>
                                <th class="col-xs-1 text-center">
                                    Tel No
                                </th>
                                <th class="col-xs-1 text-center">
                                    Pickup Loc
                                </th>
                                <th class="col-xs-1 text-center">
                                    Pickup Postcode
                                </th>
                                <th class="col-xs-1 text-center">
                                    From Happy Ice
                                </th>
                            </tr>
                            <tr>
                                <td class="col-xs-1 text-center">
                                    {{\Carbon\Carbon::parse($transaction->deliveryorder->pickup_date)->toDateString()}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->deliveryorder->pickup_timerange}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->deliveryorder->pickup_attn}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->deliveryorder->pickup_contact}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->deliveryorder->pickup_location_name}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->deliveryorder->pickup_postcode}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->deliveryorder->from_happyice ? 'Yes' : 'No'}}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-6">
                            <div class="form-group">
                                {!! Form::label('pickup_address', 'Pickup Add') !!}
                                {!! Form::textarea('pickup_address', $transaction->deliveryorder->pickup_address, ['class'=>'form-control input-sm', 'rows'=>'2']) !!}
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                {!! Form::label('pickup_comment', 'Comment', ['class'=>'control-label']) !!}
                                {!! Form::textarea('pickup_comment', $transaction->deliveryorder->pickup_comment, ['class'=>'form-control input-sm', 'rows'=>'2']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="width: 100%; height: 35px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
                <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                    Delivery Detail
                </span>
                </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <th class="col-xs-1 text-center">
                                        Delivery Date
                                    </th>
                                    <th class="col-xs-1 text-center">
                                        Time Range
                                    </th>
                                    <th class="col-xs-1 text-center">
                                        Contact Person
                                    </th>
                                    <th class="col-xs-1 text-center">
                                        Tel No
                                    </th>
                                    <th class="col-xs-1 text-center">
                                        Delivery Loc
                                    </th>
                                    <th class="col-xs-1 text-center">
                                        Delivery Postcode
                                    </th>
                                    <th class="col-xs-1 text-center">
                                        To Happy Ice
                                    </th>
                                </tr>
                                <tr>
                                    <td class="col-xs-1 text-center">
                                        {{\Carbon\Carbon::parse($transaction->deliveryorder->pickup_date)->toDateString()}}
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        {{$transaction->deliveryorder->delivery_timerange}}
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        {{$transaction->deliveryorder->delivery_attn}}
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        {{$transaction->deliveryorder->delivery_contact}}
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        {{$transaction->deliveryorder->delivery_location_name}}
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        {{$transaction->deliveryorder->delivery_postcode}}
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        {{$transaction->deliveryorder->to_happyice ? 'Yes' : 'No'}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('delivery_address', 'Delivery Add') !!}
                                    {!! Form::textarea('delivery_address', $transaction->deliveryorder->delivery_address, ['class'=>'form-control input-sm', 'rows'=>'2']) !!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('delivery_comment', 'Comment', ['class'=>'control-label']) !!}
                                    {!! Form::textarea('delivery_comment', $transaction->deliveryorder->delivery_comment, ['class'=>'form-control input-sm', 'rows'=>'2']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row" style="padding-top:20px;">
                <div style="width: 100%; height: 35px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
                <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                    Asset and Quantity
                </span>
                </div>
                <table class="table table-list-search table-hover table-bordered table-condensed">
                    <tr>
                        <th class="col-xs-1 text-center">
                            #
                        </th>
                        <th class="col-xs-1 text-center">
                            Code
                        </th>
                        <th class="col-xs-2 text-center">
                            Name
                        </th>
                        <th class="col-xs-1 text-center">
                            Brand
                        </th>
                        <th class="col-xs-1 text-center">
                            Serial No
                        </th>
                        <th class="col-xs-1 text-center">
                            Sticker
                        </th>
                        <th class="col-xs-1 text-center">
                            Comments
                        </th>
                    </tr>
                    <tbody>
                        @foreach($transactionpersonassets as $index => $data)
                        <tr ng-repeat="data in alldata">
                            <td class="col-md-1 text-center">
                                {{ $index + 1 }}
                            </td>
                            <td class="col-md-1 text-center">
                                {{$data->code}}
                            </td>
                            <td class="col-md-2 text-left">
                                {{$data->name}}
                            </td>
                            <td class="col-md-1 text-center">
                                {{$data->brand}}
                            </td>
                            <td class="col-md-1 text-left">
                                {{$data->serial_no}}
                            </td>
                            <td class="col-md-1 text-left">
                                {{$data->sticker}}
                            </td>
                            <td class="col-md-1 text-left">
                                {{$data->remarks}}
                            </td>
                        </tr>
                        @endforeach
                        @if(count($transactionpersonassets) == 0)
                            <tr>
                                <td colspan="18" class="text-center">No Records Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif

            @if(!$transaction->is_service)
            <div class="avoid">
                <div class="row">
                @if($transaction->is_deliveryorder)
                <div style="width: 100%; height: 35px; border-bottom: 1px solid black; text-align: center; margin: 15px 0 20px 0;">
                <span style="font-size: 20px; background-color: #F3F5F6; padding: 0 20px;">
                    Items
                </span>
                @endif
                @php
                    if($transaction->person->price_template_id and $transaction->person->price_template_id != '-1') {
                        $uomArr = [];
                        foreach(\App\Uom::orderBy('sequence', 'desc')->get() as $uom) {
                            foreach($deals as $deal) {
                                if($deal->qty_json and $deal->item->is_inventory and isset($deal->qty_json[$uom->name])) {
                                    if(!in_array($uom->name, $uomArr, true)){
                                        array_push($uomArr, $uom->name);
                                    }
                                }
                            }
                        }
                    }
                @endphp
                </div>
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <th class="col-xs-1 text-center" rowspan="{{$uomArr ? 2 : 0}}">
                                Item Code
                            </th>
                            <th class="col-xs-6 text-center" rowspan="{{$uomArr ? 2 : 0}}">
                                Description
                            </th>
                            @if($transaction->person->price_template_id and $transaction->person->price_template_id != '-1' and $uomArr)
                                <th class="col-xs-2 text-center" colspan="{{count($uomArr)}}">
                                    Packing
                                </th>
                            @endif
                            <th class="col-xs-2 text-center" rowspan="{{$uomArr ? 2 : 0}}">
                                @if($transaction->is_vending_generate)
                                    Sales
                                @else
                                    Quantity <br>
                                    (ctn)
                                @endif
                            </th>
                            @if($type == 'invoice')
                            <th class="col-xs-1 text-center" rowspan="{{$uomArr ? 2 : 0}}">
                                @if($transaction->is_vending_generate)
                                    Percent
                                @else
                                    Price/ ctn ({{$transaction->person->profile->currency ? $transaction->person->profile->currency->symbol: '$'}})
                                @endif
                            </th>
                            <th class="col-xs-1 text-center" rowspan="{{$uomArr ? 2 : 0}}">
                                Amount ({{$transaction->person->profile->currency ? $transaction->person->profile->currency->symbol: '$'}})
                            </th>
                            @endif
                        </tr>
                        @if($transaction->person->price_template_id and $transaction->person->price_template_id != '-1')
                        <tr>
                            @foreach($uomArr as $uomName)
                                <td class="col-xs-1 text-center">
                                    {{$uomName}}
                                </td>
                            @endforeach
                        </tr>
                        @endif

                        @php
                            $counter = 0;
                            $total_pieces = 0;
                        @endphp
                        @unless(count($deals)>0)
                        <td class="text-center" colspan="8">No Records Found</td>
                        @else
                            @foreach($deals as $index => $deal)
                                @php
                                    $pieces = 0;
                                    if($deal->divisor > 1) {
                                        $pieces = $deal->item->base_unit * $deal->dividend/ $deal->divisor;
                                    }else {
                                        $pieces = $deal->qty * $deal->item->base_unit;
                                    }
                                    $total_pieces += $pieces;

                                    $counter += 1;
                                @endphp

                            @if( $counter >= 16)
                            <tr style="page-break-inside: always">
                            @else
                            <tr>
                            @endif
                                <td class="col-xs-1 text-center">
                                    {{ $deal->item->product_id }}
                                </td>
                                <td class="col-xs-6">
                                    {{ $deal->item->name}} {{ $deal->item->remark }}
                                </td>

                                @if($deal->transaction->person->price_template_id and $deal->qty_json)
                                    @foreach($uomArr as $uomName)
                                        <td class="col-xs-1 text-right">
                                            @if(isset($deal->qty_json[$uomName]))
                                                {{$deal->qty_json[$uomName]}}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif

                                @if($deal->divisor and $deal->item->is_inventory === 1)
                                    <td class="col-xs-2 text-right">
                                        {{ $deal->divisor == 1 ? $deal->qty + 0 : ($deal->dividend + 0).'/'.($deal->divisor + 0)}}
                                    </td>
                                @elseif($deal->item->is_inventory === 0)
                                    <td class="col-xs-2 text-left">
                                        @if($deal->dividend === 1)
                                            1 Unit
                                        @else
                                            {{$deal->dividend ? $deal->dividend + 0 : 1}} Unit
                                        @endif
                                    </td>
                                @else
                                    <td class="col-xs-2 text-right">
                                        {{ $deal->qty + 0 }}
                                    </td>
                                @endif

                                @if($type == 'invoice')
                                @if($deal->unit_price == 0 || $deal->unit_price == null)
                                <td class="col-xs-1 text-right">
                                    @if($deal->qty != 0)
                                        {{ number_format(($deal->amount / $deal->dividend * $deal->divisor), 2)}}
                                    @else
                                        {{ number_format(($deal->amount), 2)}}
                                    @endif
                                </td>
                                @else
                                <td class="col-xs-1 text-right">
                                    {{ number_format($deal->unit_price, 2) }}
                                </td>
                                @endif
                                @if($deal->amount != 0)
                                <td class="col-xs-1 text-right">
                                    {{ number_format($deal->amount, 2) }}
                                </td>
                                @else
                                <td class="col-xs-1 text-right">
                                    <strong>FOC</strong>
                                </td>
                                @endif
                                @endif
                            </tr>
                            @endforeach

                        @php
                            $subtotal = 0;
                            $gst = 0;
                            $total = 0;

                            if($transaction->gst and $transaction->is_gst_inclusive) {
                                $subtotal = number_format($totalprice - ($totalprice - $totalprice/(1 + $transaction->gst_rate/100)), 2);
                                $gst = number_format(($totalprice - $totalprice/(1 + $transaction->gst_rate/100)), 2);
                                $total = number_format($totalprice, 2);
                            }else if($transaction->gst and !$transaction->is_gst_inclusive) {
                                $subtotal = number_format($totalprice, 2);
                                $gst = number_format($totalprice * $transaction->gst_rate/100, 2);
                                $total = number_format($totalprice + ($totalprice * $transaction->gst_rate/100), 2);
                            }else {
                                $total = number_format($totalprice, 2);
                            }

                            if($transaction->delivery_fee) {
                                $total += $transaction->delivery_fee;
                            }

                            // $total = number_format($total, 2);
                        @endphp

                        @if($type == 'do')
                            <tr>
                                <td colspan="2" class="text-center">
                                    Total
                                </td>
                                <td class="text-right">
                                    {{$totalqty}}
                                </td>
                            </tr>
                        @endif

                        @if($type == 'invoice')
                        @if($transaction->delivery_fee != 0)
                        <tr>
                            <td colspan="2" class="text-right">
                                <strong>Delivery Fee</strong>
                            </td>
                            <td colspan="2"></td>
                            <td class="text-right">
                                {{ number_format(($transaction->delivery_fee), 2)}}
                            </td>
                        </tr>
                        @endif

                        @if($transaction->gst and $transaction->is_gst_inclusive)
                            <tr class="noBorder">
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>GST ({{$transaction->gst_rate + 0}}%)</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    {{$gst}}
                                </td>
                            </tr>
                            <tr>
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>Total Before GST</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    {{$subtotal}}
                                </td>
                            </tr>
                            <tr>
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-xs-1 text-right">
                                    {{$totalqty}}
                                </td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{$total}}</strong>
                                </td>
                            </tr>
                        @elseif($transaction->gst and !$transaction->is_gst_inclusive)
                            <tr class="noBorder">
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>SubTotal</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    {{$subtotal}}
                                </td>
                            </tr>
                            <tr>
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>GST ({{$transaction->gst_rate + 0}}%)</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="text-right">
                                    {{$gst}}
                                </td>
                            </tr>
                            <tr class="noBorder">
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-xs-2 text-right">
                                    {{$totalqty}}
                                </td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{$total}}</strong>
                                </td>
                            </tr>
                        @else
                            <tr class="noBorder">
                                @if($uomArr)
                                    <td></td>
                                @endif
                                <td colspan="2" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-xs-2 text-right">
                                    {{$totalqty}}
                                </td>
                                <td></td>
                                <td class="text-right">
                                    <strong>{{$total}}</strong>
                                </td>
                            </tr>
                        @endif
                        @endunless
                        @endif
                    </table>
                </div>
            </div>
            @endif

        {{-- <footer class="footer"> --}}
                <div class="row">
                    @if(!$transaction->is_service)
                        <div class="col-xs-12">
                            @unless($person->cust_id[0] == 'H' or $person->cust_id[0] == 'D' or $person->is_vending or $person->is_dvm)
                                Payment by cheque should be crossed and made payable to "{{$person->profile->name}}"
                            @endunless
                        </div>
                        <div class="col-xs-12">
                            @if($person->profile->paynow_uen)
                                {{$person->profile->name}} PayNow UEN : {{$person->profile->paynow_uen}}
                            @endif
                        </div>
                    @endif
                    <div class="col-xs-12" style="padding-top:10px">
                        <div class="form-group">
                            @if($transaction->transremark or $person->remark)
                                <label class="control-label">Remarks:</label>
                                @if($person->remark)
                                    <pre>{{ $person->remark }}</pre>
                                @endif
                                @if($transaction->transremark)
                                    <pre>{{ $transaction->transremark }}</pre>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($transaction->is_service and $transaction->serviceItems)
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th class="text-center">
                                    #
                                </th>
                                <th class="text-center">
                                    Description
                                </th>
                                <th class="text-center">
                                    Photo (before)
                                </th>
                                <th class="text-center">
                                    Photo (after)
                                </th>
                            </tr>
                            @foreach($transaction->serviceItems as $serviceItemIndex => $serviceItem)
                            <tr>
                                <td class="col-xs-1 text-center">
                                    {{$serviceItemIndex + 1}}
                                    <br>
                                    @if($serviceItem->status == 1)
                                        <span class="badge badge-info">
                                            New
                                        </span>
                                    @elseif($serviceItem->status == 90)
                                        <span class="badge badge-warning" style="background-color: orange;">
                                            Incompleted
                                        </span>
                                    @elseif($serviceItem->status == 2)
                                        <span class="badge badge-success" style="background-color: green;">
                                            Completed
                                        </span>
                                    @elseif($serviceItem->status == 99)
                                        <span class="badge badge-danger" style="background-color: red;">
                                            Cancelled
                                        </span>
                                    @endif
                                </td>
                                <td class="col-xs-3 text-left">
                                    {{$serviceItem->desc}}
                                </td>
                                <td class="col-xs-4 text-center">
{{--
                                    @if($serviceItem->attachments()->where('is_primary', 1)->get())
                                        @foreach($serviceItem->attachments()->where('is_primary', 1)->get() as $attachment)
                                            <img src="{{$attachment->full_url}}" class="col-xs-12" style="width: 350px; height: 350px;">
                                        @endforeach
                                    @endif --}}
                                </td>
                                <td class="col-xs-4 text-center">
{{--
                                    @if($serviceItem->attachments()->where('is_primary', 0)->get())
                                        @foreach($serviceItem->attachments()->where('is_primary', 0)->get() as $attachment)
                                            <img src="{{$attachment->full_url}}" class="col-xs-12" style="width: 350px; height: 350px;">
                                        @endforeach
                                    @endif --}}
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @endif
{{--
                    @if($transaction->invattachments)
                    <div class="col-xs-12" style="padding-bottom: 30px;">
                        @foreach($transaction->invattachments()->oldest()->get() as $invattachment)
                            @php
                                $pathName = '';
                                if(strpos($invattachment->path, 'happyice-space.sgp1.digitaloceanspaces.com') !== false ){
                                    $pathName = $invattachment->path;
                                }else {
                                    $pathName = public_path().$invattachment->path;
                                }
                            @endphp


                            <img src="{{$pathName}}" style="width: 300px; height: 200px;">
                            <span>
                                {{$invattachment->name}}
                            </span>
                        @endforeach
                    </div>
                    @endif --}}

                    <div class="row">
                    <div class="col-xs-12" style="padding-top: 10px">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <span class="text-center col-xs-12">
                                    @if($type == 'quotation')
                                        <strong>Client Acceptance</strong>
                                    @else
                                        <strong>Goods Received in Good Conditions</strong>
                                    @endif
                                </span>
                                @if($transaction->is_deliveryorder and $transaction->sign_url)
                                    <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:10px">
                                        <img src="{{public_path().$transaction->sign_url}}" style="width: 100px; height: 100px;">
                                    </span>
                                    <span class="text-center col-xs-12" style="margin-top:0px">
                                        <strong>Customer Sign & Co. Stamp</strong>
                                    </span>
                                @else
                                    <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:40px">
                                        _______________________________
                                    </span>
                                    <span class="text-center col-xs-12" style="margin-top:0px">
                                        <strong>Customer Sign & Co. Stamp</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($type == 'invoice')
                        <div class="col-xs-6">
                            <div class="form-group">
                                <span class="text-center col-xs-12">
                                    <strong>{{$person->profile->name}}</strong>
                                </span>
                                <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:40px">
                                    _______________________________
                                </span>
                                @if(!$transaction->is_vending_generate)
                                <span class="text-center col-xs-12" style="margin-top:0px">
                                    <strong>Payment Collected By</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    </div>
                </div>
        {{-- </footer>             --}}
        </div>

    </body>
</html>