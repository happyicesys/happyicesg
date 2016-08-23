<meta charset="utf-8">
<table>
    <tbody>

    <tr>
    <th></th>
    <h4>Detailed Transaction Report</h4>
    </tr>

    @if(isset($datefrom) && isset($dateto))
        <tr>
        <th></th>
        <th>From</th><td>{{$datefrom}}</td><th>To</th><td>{{$dateto}}</td>
        </tr>
    @endif

    <tr></tr>

    <tr>
    <th>#</th>
    <th>Profile</th>
    <th>Product</th>
    <th>ID Code</th>
    <th>ID Name</th>
    <th>Inv #</th>
    <th>Delivery Date</th>
    <th>Quantity</th>
    <th>Amount</th>
    </tr>
        @foreach($deals as $index => $deal)
            <tr>
            <td>{{$index + 1}}</td>
            <td>{{$deal->profile_name}}</td>
            <td>{{$deal->product_id}}</td>
            <td>{{$deal->cust_id}}</td>
            <td>{{$deal->company}}</td>
            <td>{{$deal->id}}</td>
            <td>{{ $deal->status }}</td>
            <td>{{Carbon\Carbon::parse($deal->delivery_date)->format('d M y')}}</td>
            <td>{{$deal->qty}}</td>
            <td>{{$deal->amount}}</td>
            </tr>
        @endforeach
    </tbody>
</table>