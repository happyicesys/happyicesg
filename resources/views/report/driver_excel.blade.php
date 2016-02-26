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
    <th>Total Amount</th>
    <th>Total Qty</th>
    <th>Payment</th>
    <th>Last Modified By</th>
    <th>Last Modified Time</th>
    <th>Profile</th>
    </tr>
        @foreach($transactions as $index => $transaction)
            <tr>
            <td>{{$index + 1}}</td>
            <td>{{$transaction->id}}</td>
            <td>{{$transaction->cust_id}}</td>
            <td>{{$transaction->company}}</td>
            <td>{{$transaction->del_postcode}}</td>
            <td>{{$transaction->status}}</td>
            <td>{{$transaction->delivery_date}}</td>
            <td>{{$transaction->driver}}</td>
            <td>{{$transaction->total}}</td>
            <td>{{$transaction->total_qty}}</td>
            <td>{{$transaction->pay_status}}</td>
            <td>{{$transaction->updated_by}}</td>
            <td>{{$transaction->updated_at}}</td>
            <td>{{$transaction->profile_name}}</td>
            </tr>
        @endforeach
    </tbody>
</table>