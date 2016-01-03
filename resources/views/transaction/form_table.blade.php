 
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
                    <th class="col-md-6 text-center">
                        Item                           
                    </th>
                    <th class="col-md-2 text-center">
                        Qty                      
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
                    <tr class="txtMult">
                        <td class="col-md-6">
                            {{$price->item->product_id}} - {{$price->item->name}} - {{$price->item->remark}}
                        </td>
                        <td class="col-md-2 text-right">
                            <input type="text" name="qty[{{$price->item->id}}]" class="form-control qtyClass"/>
                        </td>
                        <td class="col-md-2">
                            <strong>
                            <input type="text" name="quote[{{$price->item->id}}]" 
                            readonly="readonly" value="{{$price->quote_price}}"
                            class="text-right form-control quoteClass" />
                            </strong>
                        </td>
                        <td class="col-md-2">
                            <input type="text" name="amount[{{$price->item->id}}]" class="text-right form-control amountClass" readonly="readonly" />
                        </td>
                    </tr>                
                    @endforeach
                    @endunless  
                    <tr>
                        <td class="col-md-1 text-center"><strong>Total</strong></td>
                        <td colspan="2" class="col-md-3 text-right">
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
   