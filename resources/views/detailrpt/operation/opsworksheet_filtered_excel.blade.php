@inject('transaction', 'App\Transaction')

<meta charset="utf-8">
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Postcode + Cust ID + ID Name</th>
        <th>Ops Note</th>
        <th>Inv#</th>
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
            <td>{{$person->del_postcode}} {{$person->cust_id}} {{$person->company}}</td>
            <td>{{$person->operation_note}}</td>
            @foreach($alldata[$indexpeople] as $data)
            @php
                $transactionsStr = '';
                $transArr = [];

                $transactions = $transaction::where('person_id', explode(",", $data['id'])[0])
                                ->whereDate('delivery_date', '=', explode(",", $data['id'])[1])
                                ->get();

                if(count($transactions) > 0) {
                    foreach($transactions as $trans) {
                        array_push($transArr, $trans->id);
                    }
                    $transactionsStr = implode(",", $transArr);
                }

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

                if($color == 'ffffff') {
                    if($data['qty']) {
                        $color = '#77d867';
                    }
                }
            @endphp
                <td>{{$transactionsStr}}</td>
                <td style="background-color: {{$color}}; border: thin solid #000000;">
                    {{$data['qty']}}
                </td>
            @endforeach

        </tr>
    @endforeach
    </tbody>
</table>