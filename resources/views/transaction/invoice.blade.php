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
            font-size: 12px;
        }
        table{
            font-size: 11px;
            font-family: 'Times New Roman';
        }    
        th{
            font-size: 12px;
        }
        footer{
            position: fixed;
            height: 300px;
            bottom: 0;
            width: 100%;
        }   
        html, body{
            height: 100%;
        } 
        pre{
            font-size: 11px;
            font-family: 'Times New Roman';
            background-color: transparent;            
        }    
    </style>
    </head>

    <body>
        <div class="container">
            <div class="col-xs-10 col-xs-offset-1">
                <h2 class="text-center"><strong>{{$profile->name}}</strong></h2>
                <h5 class="text-center" style="margin-bottom: -5px">{{$profile->address}}</h5>
                <h5 class="text-center" style="margin-bottom: -5px">Tel: {{$profile->contact}}</h5>
                <h5 class="text-center">Co Reg No: {{$profile->roc_no}}</h5>
            </div>

            <div class="row">
                <div class="col-xs-12" style="padding-top: 20px">
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-top: 3px; margin-bottom: 0px;">
                            <div><strong>Bill To:</strong></div>
                            <span class="col-xs-12">{{$person->company}}</span> 
                            <span class="col-xs-12">{{$person->bill_to}}</span>
                            <span class="col-xs-offset-1">{{$person->postcode}}</span> 
                        </div>
                        <div class="form-group" style="margin-bottom: 0px">
                            <div class="inline"><strong>Attn:</strong></div>
                            <div class="inline col-xs-offset-1">{{$person->name}}</div>
                        </div>
                        <div class="form-group">
                            <div class="inline"><strong>Tel:</strong></div> 
                            <div class="inline" style="padding-left: 20px">{{$person->contact}}</div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-left: 10px">
                            <div><strong>Send To:</strong></div>
                            <div>
                                <span class="col-xs-12">{{$person->company}}</span> 
                                <span class="col-xs-12">{{$person->del_address}}</span>
                                <span class="col-xs-offset-1">{{$person->postcode}}</span> 
                            </div>
                        </div>
                    </div> 
                    <div class="col-xs-4">
                        <div class="form-group" style="padding-left:20px; margin-top:-12px;">
                            <div style="font-size: 145%;" class="text-center">
                                <strong>DO / INVOICE</strong>
                            </div>
                            <div class="form-group" style="margin-bottom: 0px">
                                <span class="inline"><strong>DO/Inv No:</strong></span>
                                <span class="inline col-xs-offset-2">{{$transaction->id}}</span>
                            </div>
                            <div class="form-group" style="margin-bottom: 0px">
                                <span class="inline"><strong>Order On:</strong></span>
                                <span class="inline col-xs-offset-2">{{$transaction->created_at}}</span>
                            </div> 
                            <div class="form-group" style="margin-bottom: 0px">
                                <span class="inline"><strong>Delivery On:</strong></span>
                                <span class="inline col-xs-offset-1">{{$transaction->delivery_date}}</span>
                            </div> 
                            <div class="form-group" style="margin-bottom: 0px">
                                <span class="inline"><strong>Term:</strong></span>
                                <span class="inline col-xs-offset-3" style="padding-left: 8px">{{$person->payterm}}</span>
                            </div>
                            <div class="form-group" style="margin-bottom: 0px">
                                <span class="inline"><strong>Saleserson:</strong></span>
                            </div>                                                                                                                                                
                        </div>
                    </div>                               

                </div>
            </div> 

            <div class="col-xs-12" style="padding-top: 10px">
                <table class="table table-bordered table-condensed">
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
                            Unit Price ($)
                        </th>
                        <th class="col-xs-1 text-center">
                            Amount ($)
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
                        <td class="col-xs-7">
                            {{ $deal->item->name}} {{ $deal->item->remark }}
                        </td>
                        <td class="col-xs-2 text-center">
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
                            <label class="control-label">Comments:</label>
                            <pre>{{ $transaction->transremark }}</pre> 
                        </div>
                    </div>

                    <div class="col-xs-12" style="padding-top: 40px">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <span class="text-center col-xs-12">
                                    <strong>{{$profile->name}}</strong>
                                </span>
                                <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:60px">
                                    _______________________________
                                </span>
                                <span class="text-center col-xs-12" style="margin-top:0px">
                                    <strong>Authorised Singnature</strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <span class="text-center col-xs-12">
                                    <strong>GR in Good Order & Conditions</strong>
                                </span>
                                <span class="text-center col-xs-12" style="margin-bottom:-1px; padding-top:60px">
                                    _______________________________
                                </span>
                                <span class="text-center col-xs-12" style="margin-top:0px">
                                    <strong>Customer Sign & Co. Stamp</strong>
                                </span>
                            </div>                            
                        </div> 
                    </div>

                </div> 
          
        </footer>            
        </div>

    </body>
</html>    