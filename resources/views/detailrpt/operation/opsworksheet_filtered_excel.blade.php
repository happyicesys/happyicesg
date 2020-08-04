@inject('transaction', 'App\Transaction')

<meta charset="utf-8">
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Address Postcode Singapore</th>
        <th>Postcode</th>
        <th>Cust ID</th>
        <th>ID Name</th>
        <th>Category</th>
        <th>Inv#</th>
        <th>Ops Note</th>
        <th>Zone</th>
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
    @foreach($people as $indexpeople => $person)
        <tr>
            <td>{{$indexpeople + 1}}</td>
            <td>{{$person->del_address}} {{$person->del_postcode}} Singapore</td>
            <td>{{$person->del_postcode}}</td>
            <td>{{$person->cust_id}}</td>
            <td>{{$person->company}}</td>
            <td>{{$person->custcategory}}</td>
            @php
                $transactionsStr = '';
                $transArr = [];

                $transactions = $transaction::where('person_id', $person->person_id)
                                ->whereDate('delivery_date', '=', request('chosen_date'))
                                ->get();

                if(count($transactions) > 0) {
                    foreach($transactions as $trans) {
                        array_push($transArr, $trans->id);
                    }
                    $transactionsStr = implode(",", $transArr);
                }
            @endphp

            <td>{{$person->operation_note}}</td>
            <td>{{$person->zone_name}}</td>
            <td>
                {{$person->del_lat}}
            </td>
            <td>
                {{$person->del_lng}}
            </td>
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

                if($data['bool_transaction']) {
                    $color = '#77d867';
                    $data['qty'][0]->qty = 0;
                }

            @endphp
                <td style="background-color: {{$color}}; border: thin solid #000000;">
                    {{$data['qty'][0]->qty}}
                </td>
            @endforeach

        </tr>
    @endforeach
    </tbody>
</table>