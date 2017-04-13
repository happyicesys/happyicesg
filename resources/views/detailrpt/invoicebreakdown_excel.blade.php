@inject('deals', 'App\Deal')
<meta charset="utf-8">
<table>
    <tbody>
        <tr></tr>
        <tr>
            <th>Total Revenue</th>
            <td>{{number_format($deals::whereIn('transaction_id', $allTransactionsId)->sum('amount'), 2, '.', '')}}</td>
        </tr>
        <tr>
            <th>Total Cost</th>
            <td>{{number_format($deals::whereIn('transaction_id', $allTransactionsId)->sum(DB::raw('qty * unit_cost')), 2, '.', '')}}</td>
        </tr>
        <tr>
            <th>Gross Earning</th>
            <td>{{number_format($deals::whereIn('transaction_id', $allTransactionsId)->sum('amount') - $deals::whereIn('transaction_id', $allTransactionsId)->sum(DB::raw('qty * unit_cost')), 2, '.', '')}}</td>
        </tr>
    </tbody>
</table>