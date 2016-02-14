
<table>
    <tbody>
    <tr>
        <th>#</th>
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
        @foreach($people as $index => $person)
        <tr>
            <td>{{$index + 1}}</td>
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
        @endforeach
    </tbody>
</table>

