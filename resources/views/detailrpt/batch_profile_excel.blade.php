<meta charset="utf-8">
<table>
    @php
        // dd($request->all());
    @endphp
    <tbody>
{{--
        <tr>
            <th>Custcategory</th>
            <td>{{implode($request->custcategory)}} @if($request->exclude_custcategory)(exclude)@endif</td>
        </tr>
        <tr>
            <th>Custcategory Group</th>
            <td>{{implode($request->custcategory_group)}}</td>
        </tr>
        <tr>
            <th>Cust Status</th>
            <td>{{implode($request->actives)}}</td>
        </tr> --}}
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
{{--
        <tr>
            <th>Tags</th>
            <td>{{implode($request->personTags)}}</td>
        </tr> --}}
        <tr></tr>
        <tr></tr>
        <tr>
            <th>Total Revenue ($)</th>
            <td data-format="0.00">{{$totals['amount']}}</td>
        </tr>
        <tr>
            <th>Total Ice Cream Cost ($)</th>
            <td data-format="0.00">{{$totals['cost']}}</td>
        </tr>
        <tr>
            <th>Total Gross Earning ($)</th>
            <td data-format="0.00">{{$totals['gross']}}</td>
        </tr>
        @if($totals['gross'] > 0)
            <tr>
                <th>Total Gross Earning (%)</th>
                <td data-format="0">
                    {{($totals['gross']/ $totals['amount'] * 100)}}
                </td>
            </tr>
        @endif
        <tr></tr>
        <tr>
            <th align="center">Customer</th>
            <th align="center">Revenue ($)</th>
            <th align="center">Cost ($)</th>
            <th align="center">Gross Earning ($)</th>
            <th align="center">Gross Earning (%)</th>
            <th align="center">First Inv Date</th>
        </tr>
        @foreach($deals as $deal)
        <tr>
            <td>{{$deal->cust_id}} - {{$deal->company}}</td>
            <td data-format="0.00">{{$deal->amount}}</td>
            <td data-format="0.00">{{$deal->cost}}</td>
            <td data-format="0.00">{{$deal->gross}}</td>
            <td data-format="0">
                @if($deal->gross > 0)
                    {{$deal->gross/ $deal->amount * 100}}
                @endif
            </td>
            <td>{{\Carbon\Carbon::parse($deal->first_inv)->toDateString()}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
