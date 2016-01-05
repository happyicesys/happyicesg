 
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">         
            <div class="pull-left display_panel_title">
                <h3 class="panel-title"><strong>Create List : {{$person->cust_id}} - {{$person->company}}</strong></h3>
            </div>
        </div>      
    </div>

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-list-search table-hover table-bordered table-condensed">
                <tr style="background-color: #DDFDF8">                   
                    <th class="col-md-7 text-center">
                        Item                           
                    </th>
                    <th class="col-md-1 text-center">
                        Qty                      
                    </th>
                    <th class="col-md-2 text-center">
                        Retail Price ($)
                    </th>                    
                    <th class="col-md-2 text-center">
                        Quote Price ($)
                    </th>
                     <th class="col-md-2 text-center">
                        Amount ($)
                    </th>                                                                                                
                </tr>

                <tbody>

                    @unless(count($prices)>0)
                    <td class="text-center" colspan="7">No Records Found</td>
                    @else
                    @foreach($prices as $price)
                    <tr class="txtMult form-group">
                        <td class="col-md-7">
                            {{$price->item->product_id}} - {{$price->item->name}} - {{$price->item->remark}}
                        </td>
                        <td class="col-md-1 text-right">
                            <input type="text" name="qty[{{$price->item->id}}]" class="qtyClass" style="width: 80px" />
                        </td>
                        <td class="col-md-1">
                            <strong>
                            <input type="text" name="retail[{{$price->item->id}}]" 
                             value="{{$price->retail_price}}"
                            class="text-right retailClass form-control" readonly="readonly"/>
                            </strong>
                        </td>                        
                        <td class="col-md-1">
                            <strong>
                            @if($price->quote_price != '' or $price->quote_price != null or $price->quote_price != 0 )
                                <input type="text" name="quote[{{$price->item->id}}]" 
                                value="{{$price->quote_price}}"
                                class="text-right form-control quoteClass" readonly="readonly"/>
                            @else
                                <input type="text" name="quote[{{$price->item->id}}]" 
                                value="{{$price->quote_price}}"
                                class="text-right form-control quoteClass"/>
                            @endif                            
                            </strong>
                        </td>
                        <td class="col-md-2">
                            <input type="text" name="amount[{{$price->item->id}}]" 
                            class="text-right form-control amountClass" readonly="readonly" style="width: 140px" />
                        </td>
                    </tr>                
                    @endforeach
                    @endunless  
                    <tr>
                        <td class="col-md-1 text-center"><strong>Total</strong></td>
                        <td colspan="3" class="col-md-3 text-right">
                            <td class="text-right" id="grandTotal" >
                                <strong>
                                    <input type="text" name="total_create" class="text-right form-control grandTotal" readonly="readonly" />
                                </strong>
                            </td>
                        </td>
                    </tr>                                      

                </tbody>
            </table>          
        </div>
    </div>
</div>
   