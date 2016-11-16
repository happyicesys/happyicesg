<meta charset="utf-8">
<table>
    <tbody>
    <tr>
        <th>area_code</th>
        <th>area_name</th>
        <th>postcode</th>
        <th>block</th>
        <th>street</th>
        <th>AM</th>
        <th>assign_to</th>
    </tr>
        @foreach($postcodes as $postcode)
        <tr>
            <td>{{$postcode->area_code}}</td>
            <td>{{$postcode->area_name}}</td>
            <td>{{$postcode->value}}</td>
            <td>{{$postcode->block}}</td>
            <td>{{$postcode->street}}</td>
            <td>{{$postcode->group}}</td>
            <td>{{$postcode->person ? $postcode->person->name : ''}}</td>
        </tr>
        @endforeach
    </tbody>
</table>

