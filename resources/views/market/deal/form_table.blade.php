@inject('people', 'App\Person')

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                @unless($transaction->status == 'Cancelled' or $transaction->status == 'Deleted')
                <h3 class="panel-title"><strong>Create List : {{$person->cust_id}} - {{$person->company}} ({{$person->name}})</strong></h3>
                @else
                <h3 class="panel-title"><strong><del>Create List : {{$person->cust_id}} - {{$person->company}} ({{$person->name}})</del></strong></h3>
                @endunless
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
                        <td class="col-md-5 col-xs-4 hidden-xs">
                                <strong>{{$price->item->product_id}}</strong></span>
                                - {{$price->item->name}}
                                <small>{{$price->item->remark}}</small>
                        </td>
                        <td class="col-md-5 col-xs-4 hidden-lg hidden-md hidden-sm">
                                <strong>{{$price->item->product_id}}</strong><br>
                                {{$price->item->name}}<br>
                                <small>{{$price->item->remark}}</small>
                        </td>
                        <td class="col-md-1 text-right">
                            @if($transaction->status === 'Draft' or $transaction->status === 'Pending' or $transaction->status === 'Confirmed' or Auth::user()->hasRole('admin') or $transaction->person->cust_id[0] === 'H')
                                <input type="text" name="qty[{{$price->item->id}}]" class="qtyClass form-control" style="min-width: 70px"/>
                            @else
                                <input type="text" name="qty[{{$price->item->id}}]" class="qtyClass form-control" style="min-width: 70px" readonly="readonly" />
                            @endif
                        </td>
                        <td class="col-md-2">
                            @if($transaction->person->cust_id[0] === 'D')
                                <strong>
                                    <input type="text" name="quote[{{$price->item->id}}]"
                                    value="{{($person->cost_rate != 0 && $person->cost_rate) ? $person->cost_rate/ 100 * $price->quote_price : $price->quote_price}}"
                                    class="text-right form-control quoteClass" readonly="readonly"/>
                                </strong>
                            @else
                                <strong>
                                    <input type="text" name="quote[{{$price->item->id}}]"
                                    value="{{($person->cost_rate != 0 && $person->cost_rate) ? $person->cost_rate/ 100 * $price->retail_price : $price->retail_price}}"
                                    class="text-right form-control quoteClass" readonly="readonly"/>
                                </strong>
                            @endif
                        </td>
                        <td class="col-md-2">
                            <input type="text" name="amount[{{$price->item->id}}]"
                            class="text-right form-control amountClass" style="min-width: 100px;" readonly="readonly" />
                        </td>
                    </tr>
                    @endforeach
                    @endunless
                    <tr>
                        <td class="col-md-1 text-center"><strong>Total</strong></td>
                        <td colspan="2" class="col-md-2 text-right">
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
