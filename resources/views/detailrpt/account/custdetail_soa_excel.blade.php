<meta charset="utf-8">
<table>
    <tbody>

    <tr>
    <th></th>
    <th></th>
    <h4>Account SOA Report</h4>
    </tr>

    @if(isset($date1) && isset($date2))
        <tr>
        <th></th>
        <th>From</th><td>{{$date1}}</td><th>To</th><td>{{$date2}}</td>
        </tr>
    @endif

    <tr></tr>

    <tr>
    <th>Inv #</th>
    <th>Customer ID</th>
    <th>ID Name</th>
    <th>Date</th>
    <th>Charges($)</th>
    </tr>
        @foreach($data as $index => $transaction)
            @if($index != 0)
                @if($data[$index-1]->cust_id != $transaction->cust_id)
                    <tr></tr>
                @endif
            @endif
            <tr>
                <td>{{$transaction->id}}</td>
                <td>{{$transaction->cust_id}}</td>
                <td>{{$transaction->company}}</td>
                {{-- <td>{{Carbon\Carbon::createFromFormat('Y-m-d', $transaction->order_date)->format('d M y')}}</td> --}}
                <td>{{Carbon\Carbon::parse($transaction->delivery_date)->format('Y-m-d')}}</td>
                <td>
                    {{$transaction->total}}
                </td>
            </tr>
        @endforeach
        <tr>
            <th>Total Due</th>
            <td></td>
            <td></td>
            <td></td>
            <th>{{$total}}</th>
        </tr>
    </tbody>
</table>