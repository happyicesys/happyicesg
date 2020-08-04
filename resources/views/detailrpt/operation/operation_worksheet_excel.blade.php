<meta charset="utf-8">
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Postcode</th>
        <th>Cust ID</th>
        <th>ID Name</th>
        <th>Category</th>
        <th>Note</th>
        <th>Lat</th>
        <th>Lng</th>
        @foreach($dates as $date)
        <th>
            {{\Carbon\Carbon::parse($date)->format('y-m-d')}} ({{\Carbon\Carbon::parse($date)->format('D')}})
        </th>
        @endforeach
    </tr>
    </thead>

    <tbody>
        @php
            dd($people->toArray());
        @endphp
    @foreach($people as $indexpeople => $person)
        <tr>
            <td>{{$indexpeople + 1}}</td>
            <td>{{$person->del_postcode}}</td>
            <td>{{$person->cust_id}}</td>
            <td>{{$person->company}}</td>
            <td>{{$person->custcategory}}</td>
            <td>{{$person->operation_note}}</td>
            <td>{{$person->del_lat}}</td>
            <td>{{$person->del_lng}}</td>

        @foreach($alldata[$indexpeople] as $data)

            {{-- color setup --}}
            @php
                $color = $data['color'];

                switch($color) {
                    case 'Yellow':
                        $color = '#ffff00';
                        break;
                    case 'Red':
                        $color = '#ff0000';
                        break;
                    default:
                        $color = 'ffffff';
                }
            @endphp

            <td style="background-color: {{$color}}; border: thin solid #000000;">
                {{$data['qty']}}
            </td>
        @endforeach
        </tr>
    @endforeach
    </tbody>
</table>