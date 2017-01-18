<meta charset="utf-8">
<table>
    <tbody>

    <tr>
    <th></th>
    <th></th>
    <h4>Account Cust Detail Report</h4>
    </tr>
    <tr></tr>

    <tr>
    <th>#</th>
    <th>Inv #</th>
    <th>ID</th>
    <th>ID Name</th>
    <th>Status</th>
    <th>Delivery Date</th>
    <th>Total Amount</th>
    <th>Payment</th>
    <th>Payment Received Dt</th>
    <th>Profile</th>
    </tr>
        @foreach($data as $index => $transaction)
            <tr>
                <td>{{$index + 1}}</td>
                <td>{{$transaction->id}}</td>
                <td>{{$transaction->cust_id}}</td>
                <td>{{ $transaction->cust_id[0] === 'D' || $transaction->cust_id[0] === 'H' ? $transaction->name : $transaction->company }}</td>
                <td>{{ $transaction->status }}</td>
                <td>{{ Carbon\Carbon::parse($transaction->delivery_date)->format('Y-m-d') }}</td>
                <td>{{ $transaction->total }}</td>
                <td>{{ $transaction->pay_status }}</td>
                <td>{{ Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d') }}</td>
                <td>{{ $transaction->profile_name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>