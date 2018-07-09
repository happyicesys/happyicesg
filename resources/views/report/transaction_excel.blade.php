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
    <th>Delivery Date</th>
    <th>Deliver By</th>
    <th>Payment Received On</th>
    <th>Payment Received By</th>
    <th>Total Amount</th>
    <th>GST</th>
    <th>Total Qty</th>
    <th>Payment</th>
    <th>Last Modified By</th>
    <th>Last Modified Time</th>
    <th>Payment Method</th>
    <th>Note</th>
    <th>Profile</th>
    </tr>
        @foreach($transactions as $index => $transaction)
            <tr>
            <td>{{$index + 1}}</td>
            <td>{{$transaction->id}}</td>
            <td>{{$transaction->person->cust_id}}</td>
            <td>{{$transaction->person->company}}</td>
            <td>{{$transaction->person->del_postcode}}</td>
            <td>{{$transaction->status}}</td>
            <td>{{$transaction->delivery_date}}</td>
            <td>{{$transaction->driver}}</td>
            <td>{{$transaction->paid_at ? $transaction->paid_at : '-'}}</td>
            <td>{{$transaction->paid_by ? $transaction->paid_by : '-'}}</td>
            <td>
{{--
            @if($transaction->person->profile->gst)
                {{ number_format(($transaction->total * 107/100), 2, '.', ',') }}
            @else
                {{$transaction->total}}
            @endif   --}}
            {{$transaction->total}}
            </td>
            <td>
                @if($transaction->gst)
                    {{ number_format(($transaction->total * $transaction->gst_rate/100), 2, '.', ',') }}
                @else
                    -
                @endif
            </td>
            <td>{{$transaction->total_qty}}</td>
            <td>{{$transaction->pay_status}}</td>
            <td>{{$transaction->updated_by}}</td>
            <td>{{$transaction->updated_at}}</td>
            <td>{{$transaction->pay_method ? $transaction->pay_method : '-'}}</td>
            <td>{{$transaction->note ? $transaction->note : '-'}}</td>
            <td>{{$transaction->person->profile->name}}</td>
            </tr>
        @endforeach
    </tbody>
</table>