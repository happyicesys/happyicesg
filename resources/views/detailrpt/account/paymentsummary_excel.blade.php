<meta charset="utf-8">
<table>
    <tbody>

    <tr></tr>
    <tr>
        <th></th>
        <th>Happyice P/L</th>
        <th></th>
        <th></th>
        <th>Happyice Logistic P/L</th>
        <th></th>
        <th></th>
        <th>All Profile</th>
        <th></th>
        <th></th>
    </tr>

    <tr>
        <th></th>
        <th>Cash:</th>
        <th>{{$data['total_cash_happyice'] ? $data['total_cash_happyice'] : 0.00}}</th>
        <th></th>
        <th>Cash:</th>
        <th>{{$data['total_cash_logistic'] ? $data['total_cash_logistic'] : 0.00}}</th>
        <th></th>
        <th>Cash:</th>
        <th>{{$data['total_cash_all'] ? $data['total_cash_all'] : 0.00}}</th>
        <th></th>
    </tr>

    <tr>
        <th></th>
        <th>Cheque:</th>
        <th>{{$data['total_cheque_happyice'] ? number_format($data['total_cheque_happyice'], 2, '.', '') : 0.00}}</th>
        <th></th>
        <th>Cheque:</th>
        <th>{{$data['total_cheque_logistic'] ? number_format($data['total_cheque_logistic'], 2, '.', '') : 0.00}}</th>
        <th></th>
        <th>Cheque:</th>
        <th>{{$data['total_cheque_all'] ? number_format($data['total_cheque_all'], 2, '.', '') : 0.00}}</th>
        <th></th>
    </tr>

    <tr>
        <th></th>
        <th>TT:</th>
        <th>{{$data['total_tt_happyice'] ? number_format($data['total_tt_happyice'], 2, '.', '') : 0.00}}</th>
        <th></th>
        <th>TT:</th>
        <th>{{$data['total_tt_logistic'] ? number_format($data['total_tt_logistic'], 2, '.', '') : 0.00}}</th>
        <th></th>
        <th>TT:</th>
        <th>{{$data['total_tt_all'] ? number_format($data['total_tt_all'], 2, '.', '') : 0.00}}</th>
        <th></th>
    </tr>

    <tr></tr>
    <tr>
        <th>#</th>
        <th>Pay Received Date</th>
        <th>Pay Method</th>
        <th>Total</th>
        <th>Profile</th>
        <th>Bank in Date</th>
        <th>Remark</th>
        <th>Updated By</th>
    </tr>
    @foreach($data['transactions'] as $index => $transaction)
        <tr>
            <td>{{$index + 1}}</td>
            <td>{{$transaction->payreceived_date}}</td>
            <td>{{$transaction->pay_method}}</td>
            <td>{{$transaction->total}}</td>
            <td>{{$transaction->profile}}</td>
            <td>{{$transaction->bankin_date}}</td>
            <td>{{$transaction->remark}}</td>
            <td>{{$transaction->name}}</td>
        </tr>
    @endforeach


    </tbody>
</table>