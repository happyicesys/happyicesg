<meta charset="utf-8">
<table>
    <tbody>
        <tr>
            <th>Inv Status</th>
            <td>{{implode(",", $request->statuses)}}</td>
        </tr>
        <tr>
            <th>Delivery From</th>
            <td>{{$request->delivery_from}}</td>
        </tr>
        <tr>
            <th>Delivery To</th>
            <td>{{$request->delivery_to}}</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th>Customer</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2">
                    {{$summaryDeal->cust_id}} - {{$summaryDeal->company}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>CustCat</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2">
                    {{$summaryDeal->custcategory_name}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>CustCatGroup</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2">
                    {{$summaryDeal->custcategory_group_name}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Revenue ($)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" align="right" data-format="0.00">
                    {{$summaryDeal->amount}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Cost ($)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" align="right" data-format="0.00">
                    {{$summaryDeal->cost}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Gross ($)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" align="right" data-format="0.00">
                    {{$summaryDeal->gross}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Gross (%)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" align="right" data-format="0">
                    @if($summaryDeal->gross > 0)
                        {{$summaryDeal->gross/ $summaryDeal->amount * 100}}
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <th>First Inv Date</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" align="center">
                    {{$summaryDeal->first_inv}}
                </td>
            @endforeach
        </tr>
        <tr></tr>
        <tr>
            <th align="center">Item</th>
            @foreach($summaryDeals as $summaryDeal)
                <th align="center">Total Qty</th>
                <th align="center">Total ($)</th>
            @endforeach
        </tr>
        @foreach($items as $item)
        <tr>
            <td>
                {{$item->product_id}} - {{$item->name}}
            </td>

            @foreach($summaryDeals as $summaryDeal)
                @php
                    $dealResult = clone $dealsDetailQuery;
                    $dealResult =  $dealResult
                                    ->where('people.id', $summaryDeal->person_id)
                                    ->where('items.id', $item->item_id)
                                    ->select(
                                        DB::raw('SUM(qty) AS qty'),
                                        DB::raw('SUM(amount) AS amount')
                                    )
                                    ->first();
                @endphp
                <td @if($dealResult->qty > 0) data-format="0.0000" align="right" @endif>
                    @if($dealResult->qty > 0)
                        {{number_format($dealResult->qty, 4)}}
                    @endif
                </td>
                <td @if($dealResult->amount > 0) data-format="0.00" align="right" @endif>
                    @if($dealResult->amount > 0)
                        {{number_format($dealResult->amount, 2)}}
                    @endif
                </td>
            @endforeach
        </tr>
        @endforeach
        <tr>
            <th align="center">Total</th>
            @foreach($summaryDeals as $summaryDeal)
                <th align="right" data-format="0.0000">
                    {{$summaryDeal->qty}}
                </th>
                <th align="right" data-format="0.00">
                    {{$summaryDeal->amount}}
                </th>
            @endforeach
        </tr>
    </tbody>
</table>
