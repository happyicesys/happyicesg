<meta charset="utf-8">
<table>
    <tbody>
    <tr>
        <th>#</th>
        <th>Postcode</th>
        <th>Cust ID</th>
        <th>ID Name</th>
        <th>Category</th>
        <th>Note</th>
        @foreach($dates as $date)
        <th>
            {{\Carbon\Carbon::parse($date)->format('yy-mm-dd')}}
            {{\Carbon\Carbon::parse($date)->format('EEE')}}
        </th>
        @endforeach
    </tr>
{{--     @foreach($items as $item)
        @foreach($profiles as $profile)
        <tr>
            <td>{{$i}}</td>
            <td>{{$item->product_id}}</td>
            <td>{{$item->name}}</td>
            <td>{{$profile->name}}</td>
            <td>{{$unitcost::whereProfileId($profile->id)->whereItemId($item->id)->first()['unit_cost']}}</td>
        </tr>
        @endforeach
    @endforeach --}}
    </tbody>
</table>