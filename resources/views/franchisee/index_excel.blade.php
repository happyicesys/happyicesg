<meta charset="utf-8">
<table>
    <tbody>

    <tr>
        <th></th>
        <th>Total $ Collection:</th>
        <th>{{$data['totals']['total_vend_amount'] ? number_format($data['totals']['total_vend_amount'], 2, '.', '') : 0.00}}</th>
    </tr>

    <tr></tr>
    <tr>
        <th>#</th>
        <th>Ref #</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Time</th>
        <th>Resettable Clock</th>
        <th>Accumulative Clock</th>
        <th>Sales (pcs)</th>
        <th>Avg $/ pc</th>
        <th>$ Collected</th>
        <th>Updated By</th>
        <th>Remarks</th>
        <th>Bankin Date</th>
    </tr>
    @foreach($data['ftransactions'] as $index => $ftransaction)
        <tr>
            <td>{{$index + 1}}</td>
            <td>{{$ftransaction->user_code}} {{$ftransaction->ftransaction_id}}</td>
            <td>{{$ftransaction->cust_id}} - {{$ftransaction->company}}</td>
            <td>{{$ftransaction->collection_date}}</td>
            <td>{{$ftransaction->collection_time}}</td>
            <td>{{$ftransaction->digital_clock}}</td>
            <td>{{$ftransaction->analog_clock}}</td>
            <td>{{$ftransaction->sales}}</td>
            <td>{{$ftransaction->avg_sales_piece}}</td>
            <td>{{$ftransaction->total}}</td>
            <td>{{$ftransaction->updated_by}}</td>
            <td>{{$ftransaction->remarks}}</td>
            <td>{{$ftransaction->bankin_date ? \Carbon\Carbon::parse($ftransaction->bankin_date)->toDateString() : null}}</td>
        </tr>
    @endforeach
    </tbody>
</table>