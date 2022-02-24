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
        <th>Last2</th>
        <th>Last</th>
        @foreach($dates as $date)
        <th>
            {{\Carbon\Carbon::parse($date)->format('y-m-d')}} ({{\Carbon\Carbon::parse($date)->format('D')}})
        </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @if($people)
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
                <td>
                    {{$person->ops2_deldate}}<br>
                    {{$person->ops2_day}}<br>
                    {{$person->ops2_total_qty}}<br>
                    {{$person->ops2_total}}
                </td>
                <td>
                    {{$person->ops_deldate}}<br>
                    {{$person->ops_day}}<br>
                    {{$person->ops_total_qty}}<br>
                    {{$person->ops_total}}
                </td>

            @if($alldata[$indexpeople])
                @foreach($alldata[$indexpeople] as $data)
                    @php
                        $color = $data['color'];

                        switch($color) {
                            case 'Yellow':
                                $color = '#ffff33';
                                break;
                            case 'Red':
                                $color = '#ff3333';
                                break;
                            case 'Orange':
                                $color = '#ffae1a';
                                break;
                            case 'Green':
                                $color = '#00b300';
                                break;
                            default:
                                $color = '#FFFFFF';
                        }

                    @endphp

                    <td style="background-color: {{$color}}; border: thin solid #000000;">
                        {{$data['qty'][0]->qty}}
                    </td>
                @endforeach
            @endif
            </tr>
        @endforeach
    @endif
    </tbody>
</table>