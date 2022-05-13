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
                <td colspan="2" data-format="0.00">
                    {{$summaryDeal->amount}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Cost ($)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" data-format="0.00">
                    {{$summaryDeal->cost}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Gross ($)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" data-format="0.00">
                    {{$summaryDeal->gross}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th>Total Gross (%)</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2" data-format="0">
                    @if($summaryDeal->gross > 0)
                        {{$summaryDeal->gross/ $summaryDeal->amount * 100}}
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <th>First Inv Date</th>
            @foreach($summaryDeals as $summaryDeal)
                <td colspan="2">
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
                <td data-format="0.0000">
                    {{$dataArr[$summaryDeal->person_id][$item->id]['qty']}}
                </td>
                <td data-format="0.00">
                    {{$dataArr[$summaryDeal->person_id][$item->id]['amount']}}
                </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
