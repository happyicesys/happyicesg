<meta charset="utf-8">
<table>
    @if(count($importStatusArr['failure']) > 0)
    <tr>
        <th>
            <strong>
                Failure
            </strong>
        </th>
    </tr>
    <tr>
        <th>#</th>
        <th>PO Num</th>
        <th>Cust ID</th>
        <th>Del Postcode</th>
        <th>Reason</th>
        <th>Row Number</th>

    </tr>
    @foreach($importStatusArr['failure'] as $index => $data)
        <tr>
            <td>{{$index + 1}}</td>
            <td>{{$data['po_no']}}</td>
            <td>{{$data['cust_id']}}</td>
            <td>{{$data['del_postcode']}}</td>
            <td>{{$data['reason']}}</td>
            <td>{{$data['row_number']}}</td>
        </tr>
    @endforeach
    @endif

    <tr></tr>
    <tr></tr>

    @if(count($importStatusArr['item_failure']) > 0)
    <tr>
        <th>
            <strong>
                Item(s) Failure
            </strong>
        </th>
    </tr>
    <tr>
        <th>#</th>
        <th>Inv#</th>
        <th>PO Num</th>
        <th>Cust ID</th>
        <th>Del Postcode</th>
        <th>Item</th>
        <th>Qty</th>
        <th>Reason</th>
    </tr>
    @foreach($importStatusArr['item_failure'] as $index => $data)
        <tr>
            <td>{{$index + 1}}</td>
            <td>{{$data['transaction_id']}}</td>
            <td>{{$data['po_no']}}</td>
            <td>{{$data['cust_id']}}</td>
            <td>{{$data['del_postcode']}}</td>
            <td>{{$data['item']}}</td>
            <td>{{isset($data['qty']) ? $data['qty'] : ''}}</td>
            <td>{{$data['reason']}}</td>
        </tr>
    @endforeach
    @endif

    <tr></tr>
    <tr></tr>

    @if(count($importStatusArr['success']) > 0)
    <tr>
        <th>
            <strong>
                Succeed
            </strong>
        </th>
    </tr>
    <tr>
        <th>#</th>
        <th>Inv#</th>
        <th>PO Num</th>
        <th>Cust ID</th>
        <th>Del Postcode</th>
    </tr>
    @foreach($importStatusArr['success'] as $index => $data)
        <tr>
            <td>{{$index + 1}}</td>
            <td>{{$data['transaction_id']}}</td>
            <td>{{$data['po_no']}}</td>
            <td>{{$data['cust_id']}}</td>
            <td>{{$data['del_postcode']}}</td>
        </tr>
    @endforeach
    @endif

</table>