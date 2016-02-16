<meta charset="utf-8">
<table>
    <tbody>

    <tr>
    <th></th>
    <h4>Transaction Report</h4>
    </tr>

    @if(isset($date1) && isset($date2))
        <tr>
        <th></th>
        <th>From</th><td>{{$date1}}</td><th>To</th><td>{{$date2}}</td>
        </tr>
    @endif

    <tr></tr>

    <tr>
    <th>#</th>
    <th>Inv #</th>
    <th>ID</th>
    <th>ID Name</th>
    <th>Del Postcode</th>
    <th>Status</th>
    <th>Deliver Date</th>
    <th>Deliver By</th>
    <th>Payment</th>
    <th>Last Modified By</th>
    <th>Last Modified Time</th>
    <th>Profile</th>
    </tr>
        {{$mon_subtotal = 0}}
        {{$fin_subtotal = 0}}
        {{$out_subtotal = 0}}
        @foreach($transactions as $index => $transaction)
            <tr>
            <th>{{$index + 1}}</th>
            <th>{{$transaction->id}}</th>
            <th>{{$transaction->person->cust_id}}</th>
            <th>{{$transaction->person->company}}</th>
            <th>{{$transaction->person->del_postcode}}</th>
            <th>{{$transaction->status}}</th>
            <th>{{$transaction->delivery_date}}</th>
            <th>{{$transaction->driver}}</th>
            <th>{{$transaction->pay_status}}</th>
            <th>{{$transaction->updated_by}}</th>
            <th>{{$transaction->updated_at}}</th>
            <th>{{$transaction->person->profile->name}}</th>

            </tr>
                @if(count($transaction->deals) > 0)
                    <tr></tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Item Code</td>
                        <td>Item Name</td>
                        <td>Item Remark</td>
                        <td>Qty</td>
                        <td>Unit Price</td>
                        <td>Amount</td>
                    </tr>
                    {{$subtotal = 0}}
                    @foreach($transaction->deals as $deal)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{$deal->item->product_id}}</td>
                        <td>{{$deal->item->name}}</td>
                        <td>{{$deal->item->remark}}</td>
                        <td>{{ $deal->qty + 0 }}  {{ $deal->item->unit }}</td>
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
                    {{$subtotal += $deal->amount}}
                    @endforeach

                        @if($transaction->person->profile->gst)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <strong>SubTotal</strong>
                            </td>
                            <td>
                                <strong>{{ $subtotal }}</strong>
                            </td>                                                    
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                        
                            <td>
                                <strong>GST (7%)</strong>
                            </td>
                            <td class="text-right">
                                {{ number_format(($subtotal * 7/100), 2, '.', ',')}}
                            </td>                            
                        </tr>
                        @endif
                        
                        <tr>
                        @if($transaction->person->profile->gst)
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                         
                            <td>
                                <strong>Total</strong>
                            </td>
                            <td>
                                <strong>{{ number_format(($subtotal * 107/100), 2, '.', ',') }}</strong>
                            </td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                         
                            <td>
                                <strong>Total</strong>
                            </td>
                            <td class="text-right">
                                <strong>{{ $subtotal }}</strong>
                            </td>
                        @endif 
                        </tr>
                        {{$subtotal = 0}}                                           
                    <tr></tr>
                    <tr></tr>
                @else
                    <tr></tr>
                @endif
        @endforeach
    </tbody>
</table>