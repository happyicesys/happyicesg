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
                        <td class="col-md-7">
                            {{$price->product_id}} - {{$price->name}} - {{$price->remark}}
                        </td>
                        <td class="col-md-1 text-right">
                            @if($transaction->status === 'Pending' or ($transaction->person->cust_id[0] === 'D' and $people::where('user_id', Auth::user()->id)->first() ? $people::where('user_id', Auth::user()->id)->first()->cust_type !== 'AB' : true and $transaction->status === 'Confirmed' and \Carbon\Carbon::today() > \Carbon\Carbon::parse($transaction->delivery_date)->subDay()) or $transaction->status === 'Draft' or Auth::user()->hasRole('admin') or $transaction->person->cust_id[0] === 'H')
                            <input type="text" name="qty[{{$price->item_id}}]" class="qtyClass" style="width: 80px" />
                            @else
                                <input type="text" name="qty[{{$price->item_id}}]" class="qtyClass" style="width: 80px" readonly="readonly" />
                            @endif
                        </td>
                        <td class="col-md-2">
                            @if($transaction->person->cust_id[0] === 'D')
                                <strong>
                                    <input type="text" name="quote[{{$price->item_id}}]"
                                    value="{{($person->cost_rate != 0 && $person->cost_rate) ? $person->cost_rate/ 100 * $price->quote_price : $price->quote_price}}"
                                    class="text-right form-control quoteClass" readonly="readonly"/>
                                </strong>
                            @else
                                <strong>
                                    <input type="text" name="quote[{{$price->item_id}}]"
                                    value="{{($person->cost_rate != 0 && $person->cost_rate) ? $person->cost_rate/ 100 * $price->retail_price : $price->retail_price}}"
                                    class="text-right form-control quoteClass" readonly="readonly"/>
                                </strong>
                            @endif
                        </td>
                        <td class="col-md-2">
                            <input type="text" name="amount[{{$price->item_id}}]"
                            class="text-right form-control amountClass" readonly="readonly" />
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
