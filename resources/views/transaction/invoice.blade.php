<!DOCTYPE html>
<html lang="en">
    <head>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    {{-- <link rel="stylesheet" href="../bootstrap-css/bootstrap.min.css"/>  --}}
    <style type="text/css">
        .inline {
            display:inline;
        }
        body{
            font-size: 15px;
        }
        table{
            font-size: 15px;
            font-family: 'Times New Roman';
        }    
        th{
            font-size: 15px;
        }
        footer{
            position: fixed;
            height: 400px;
            bottom: 0;
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

    </style>
    </head>

    <body>
        <div class="container-fluid">
            <div class="col-xs-10 col-xs-offset-1" style="font-size:16px">
                <h2 class="text-center"><strong>{{$profile->name}}</strong></h2>
                <h5 class="text-center" style="margin-bottom: -5px">{{$profile->address}}</h5>
                <h5 class="text-center" style="margin-bottom: -5px">Tel: {{$profile->contact}}</h5>
                <h5 class="text-center">Co Reg No: {{$profile->roc_no}}</h5>
            </div>

            <div class="row no-gutter">
                <div class="col-xs-12" style="padding-top: 50px">
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                            <div><strong>Bill To:</strong></div>
                            <div style="border: solid thin; height:135px; padding-bottom: 15px;">
                            <span class="col-xs-12"> {{$person->cust_id}}, {{$person->com_remark}}</span> 
                            <span class="col-xs-12">{{$person->company}}</span>
                            <span class="col-xs-12">{{$person->bill_address}}</span>
                            <span class="col-xs-offset-1">{{$person->bill_postcode}}</span> 
                            </div>
                        </div>
                        <div style="padding-top:50px">
                            <div class="form-group" style="margin-bottom: 0px">
                                <div class="inline"><strong>Attn:</strong></div>
                                <div class="inline col-xs-offset-1">{{$person->name}}</div>
                            </div>
                            <div class="form-group">
                                <div class="inline"><strong>Tel:</strong></div> 
                                <div class="inline" style="padding-left: 20px">{{$person->contact}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group" style="padding: 3px 0px 0px 10px">
                            <div><strong>Send To:</strong></div>
                            <div style="border: solid thin; height:135px; padding-bottom: 15px;">
                                @if($person->site_name)
                                <span class="col-xs-12">{{$person->site_name}}</span>
                                @endif
                                <span class="col-xs-12">{{$person->del_address}}</span>
                                <span class="col-xs-offset-1">{{$person->del_postcode}}</span> 
                            </div>
                        </div>
                    </div> 
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-left:10px; margin-top:-12px;">
                            <div class="col-xs-12 row">
                                <div style="font-size: 145%;" class="text-center">
                                    <strong>DO / INVOICE</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline" style="font-size: 85%;"><strong>DO/Inv No:</strong></span>
                                        {{-- <span class="inline col-xs-offset-1">{{$transaction->id}}</span> --}}
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline" style="font-size: 85%;"><strong>Order On:</strong></span>
                                        {{-- <span class="inline col-xs-offset-2">{{$transaction->order_date}}</span> --}}
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline" style="font-size: 85%;"><strong>Delivery On:</strong></span>
                                        {{-- <span class="inline col-xs-offset-1">{{$transaction->delivery_date}}</span> --}}
                                    </div>                                                    
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline" style="font-size: 85%;"><strong>Term:</strong></span>
                                        {{-- <span class="inline col-xs-offset-3" style="padding-left: 8px;">{{$person->payterm}}</span> --}}
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline" style="font-size: 85%;"><strong>Modified by:</strong></span>
                                        {{-- <span class="inline col-xs-offset-3" style="padding-left: 8px;">{{$transaction->updated_by}}</span>                             --}}
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <span class="inline">{{$transaction->id}}</span>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline">{{$transaction->order_date}}</span>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline">{{$transaction->delivery_date}}</span>
                                    </div>                                                    
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline">{{$person->payterm}}</span>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0px">
                                        <span class="inline">{{$transaction->updated_by}}</span>
                                    </div>
                                </div>
                            </div>                                                                 
                        </div>
                    </div>                                                   

                </div>
            </div> 

            <div class="col-xs-12" style="padding-top: 80px">
                <table class="table table-bordered">
                    <tr>
                        <th class="col-xs-1 text-center">
                            Item Code
                        </th>
                        <th class="col-xs-8 text-center">
                            Description
                        </th>
                        <th class="col-xs-1 text-center">
                            Quantity
                        </th>
                        <th class="col-xs-1 text-center">
                            Unit Price (S$)
                        </th>
                        <th class="col-xs-1 text-center">
                            Amount (S$)
                        </th>
                    </tr>
                    
                    @unless(count($deals)>0)
                    <td class="text-center" colspan="8">No Records Found</td>
                    @else
                    @foreach($deals as $deal)
                    <tr>
                        <td class="col-xs-1 text-center">
                            {{ $deal->item->product_id }}
                        </td>
                        <td class="col-xs-8">
                            {{ $deal->item->name}} {{ $deal->item->remark }}
                        </td>
                        <td class="col-xs-1 text-center">
                            {{ $deal->qty }}  {{ $deal->item->unit }}
                        </td>                   
                        <td class="col-xs-1 text-right">
                            {{ number_format(($deal->amount / $deal->qty), 2, '.', ',')}}
                        </td>  
                        <td class="col-xs-1 text-right">
                            {{ $deal->amount }}
                        </td>                                                                    
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">
                            <span class="col-xs-offset-2"><strong>Total</strong></span>
                        </td>
                        <td class="text-right">
                            <strong>{{ $totalprice }}</strong>
                        </td>
                    </tr>
                    @endunless                          
                </table>
            </div>   

        <footer class="footer">

                <div class="col-xs-12">
                    <div class="col-xs-12">
                        Payment by cheque should be crossed and made payable to "{{$profile->name}}"
                    </div>
                    <div class="col-xs-8" style="padding-top:15px">
                        <div class="form-group">
                            @if($transaction->transremark)
                                <label class="control-label">Comments:</label>
                                <pre>{{ $transaction->transremark }}</pre> 
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12" style="padding-top: 40px">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <span class="text-center col-xs-12">
                                    <strong>Goods Received in Good Conditions</strong>
                                </span>
                                <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:60px">
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
                                    <strong>{{$profile->name}}</strong>
                                </span>
                                <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:60px">
                                    _______________________________
                                </span>
                                <span class="text-center col-xs-12" style="margin-top:0px">
                                    <strong>Payment Collected By</strong>
                                </span>
                            </div>                            
                        </div> 
                    </div>

                </div> 
          
        </footer>            
        </div>

    </body>
</html>    