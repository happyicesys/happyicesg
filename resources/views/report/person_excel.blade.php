
<table>
    <tbody>
    <tr>
        <th>Customer</th>
    </tr>
    <tr>
        <th>ID</th>
        <th>ID Name</th>
        <th>Company</th>
        <th>Site Name</th>
        <th>Billing Address</th>
        <th>Delivery Address</th>
        <th>Delivery Postcode</th>
        <th>Attn To</th>
        <th>Contact</th>

        <th>Alt Contact</th>
        <th>Email</th>
        <th>Cost Rate</th>
        <th>Terms</th>
        <th>Profile</th>
        <th>Remark</th>
        <th>Active</th>
    </tr>

    <tr>
        <td>{{$person->cust_id}}</td>
        <td>{{$person->company}}</td>
        <td>{{$person->com_remark}}</td>
        <td>{{$person->site_name}}</td>
        <td>{{$person->bill_address}}</td>
        <td>{{$person->del_address}}</td>
        <td>{{$person->del_postcode}}</td>
        <td>{{$person->name}}</td>
        <td>{{$person->contact}}</td>

        <td>{{$person->alt_contact}}</td>
        <td>{{$person->email}}</td>
        <td>{{$person->cost_rate}}</td>
        <td>{{$person->payterm}}</td>
        <td>{{$person->profile->name}}</td>
        <td>{{$person->remark}}</td>
        <td>{{$person->active}}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th>Transaction</th>
    </tr>
    <tr>
        <th>#</th>
        <th>Inv #</th>
        <th>Status</th>
        <th>Deliver By</th>
        <th>Deliver Date</th>
        <th>Total Amount</th>
        <th>Payment</th>
        <th>Last Modified By</th>
        <th>Last Modified Date</th>
    </tr>

    @foreach($transactions as $index => $transaction)
    <tr>
        <td>{{$index + 1}}</td>
        <td>{{$transaction->id}}</td>
        <td>{{$transaction->status}}</td>
        <td>{{$transaction->driver}}</td>
        <td>{{$transaction->delivery_date}}</td>
        <td>{{$transaction->total}}</td>
        <td>{{$transaction->pay_status}}</td>
        <td>{{$transaction->updated_by}}</td>
        <td>{{$transaction->updated_at}}</td>
    </tr>
    @endforeach
    </tbody>
</table>

