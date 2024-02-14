@inject('stransactions', 'App\Transaction')
@inject('sprofiles', 'App\Profile')
@inject('sdeals', 'App\Deal')
@inject('speople', 'App\Person')
@inject('scustcategories', 'App\Custcategory')
@inject('sitems', 'App\Item')
<meta charset="utf-8">
<table>
    <tbody>
        @if($request)
            <tr>
                <th style="border: 1px solid #000000">
                    {{request('profile_id') ? $sprofiles::where('id', request('profile_id'))->first()->name : 'All'}}
                </th>
                <th style="border: 1px solid #000000">
                    {{request('stock_status')}}
                </th>
                @if(request('delivery_from') and request('delivery_to'))
                    <th></th>
                    <th style="border: 1px solid #000000">
                        {{request('delivery_from')}}
                    </th>
                    <th>to</th>
                    <th style="border: 1px solid #000000">
                        {{request('delivery_to')}}
                    </th>
                @endif
            </tr>
            <tr>
                <th style="border: 1px solid #000000">
                    ID
                </th>
                <th style="border: 1px solid #000000">
                    {{request('cust_id')}}
                </th>
                <th></th>
                <th style="border: 1px solid #000000">
                    Prefix Code
                </th>
                <th style="border: 1px solid #000000">
                    {{request('prefix_code')}}
                </th>
                <th></th>
                <th style="border: 1px solid #000000">
                    Company
                </th>
                <th style="border: 1px solid #000000">
                    {{request('company')}}
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid #000000">
                    Customer
                </th>
                <th style="border: 1px solid #000000">
                    {{request('person_id') ? $speople::where('id', request('person_id'))->first()->company : 'All'}}
                </th>
                <th></th>
                <th style="border: 1px solid #000000">
                    Cust Category
                </th>
                <th style="border: 1px solid #000000">
                    {{request('custcategory_id') ? $scustcategories::where('id', request('custcategory_id'))->first()->name : 'All'}}
                </th>
            </tr>
        @endif
        <tr></tr>
        <tr></tr>
        <tr>
            <th>Total Revenue $</th>
            <td data-format="0.00">
                {{number_format($sdeals::whereIn('transaction_id', $allDateTransactionIds)->sum(DB::raw('ROUND(amount, 2)')), 2)}}
            </td>
        </tr>
        <tr>
            <th>Total Qty</th>
            <td data-format="0.00">
                {{number_format($sdeals::whereIn('transaction_id', $allDateTransactionIds)->sum('qty'), 2)}}
            </td>
        </tr>

        <tr></tr>

        <tr>
            <th align="center">
                #
            </th>
            <th align="center">
                Item ID
            </th>
            <th align="center">
                Product
            </th>
            <th align="center">
                Unit
            </th>
            <th align="center">
                Inventory Item
            </th>
            <th align="center">
                Total Qty
            </th>
            @foreach($allDatesArr as $date)
                <th align="center">
                    {{ \Carbon\Carbon::parse($date)->toDateString() }}
                </th>
            @endforeach
        </tr>

        @foreach($items = $sitems::whereIn('id', $itemsIdArr)->orderBy('product_id')->get() as $index => $item)
        <tr>
            <td align="center">
                {{$index + 1}}
            </td>
            <td align="center">
                {{$item->product_id}}
            </td>
            <td align="left">
                {{$item->name}}
            </td>
            <td align="center">
                {{$item->unit}}
            </td>
            <td align="center">
                {{$item->is_inventory ? 'Yes' : 'No'}}
            </td>
            <td align="right" data-format="0.0000">
                {{
                    \DB::table('deals')
                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                    ->whereIn('transactions.id', $allDateTransactionIds)
                    ->where('items.id', $item->id)
                    ->sum('qty')
                }}
            </td>

            @php
                $cfdeduct = 0;
            @endphp

                @if(request('stock_status') === 'Balance')
                    @php
                        $cfdeduct = \DB::table('deals')
                                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                    ->whereIn('transactions.id', $allDateTransactionIds)
                                    ->where('items.id', $item->id)
                                    ->sum('qty');
                    @endphp
                    @foreach($allDatesArr as $date)
                    @php
                        $currentDateTransactionIds = [];
                        $currentDateTransactions = $stransactions::whereIn('id', $allDateTransactionIds)->where('delivery_date', $date)->get();
                        foreach($currentDateTransactions as $currentDateTransaction) {
                            array_push($currentDateTransactionIds, $currentDateTransaction->id);
                        }
/*                        $cfdeduct -= \DB::table('deals')
                                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                        ->whereIn('deals.transaction_id', $currentDateTransactionIds)
                                        ->where('items.id', $item->id)
                                        ->sum('qty');*/
                        $balance = 0;

                        $balance = \DB::table('deals')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->whereIn('transactions.id', $currentDateTransactionIds)
                            ->where('items.id', $item->id)
                            ->latest('deals.created_at')
                            ->first()
                            ?
                            \DB::table('deals')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->whereIn('transactions.id', $currentDateTransactionIds)
                            ->where('items.id', $item->id)
                            ->latest('deals.created_at')
                            ->first()
                            ->qty_before
                            :
                            0;
                    @endphp
                    <td align="right" data-format="0.0000">
                        {{$balance}}
                    </td>
                    @endforeach
                @else
                    @foreach($allDatesArr as $date)
                    @php
                        $currentDateTransactionIds = [];
                        $currentDateTransactions = $stransactions::whereIn('id', $allDateTransactionIds)->where('delivery_date', $date)->get();
                        foreach($currentDateTransactions as $currentDateTransaction) {
                            array_push($currentDateTransactionIds, $currentDateTransaction->id);
                        }
                    @endphp
                    <td align="right" data-format="0.0000">
                    {{
                        \DB::table('deals')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->whereIn('transactions.id', $currentDateTransactionIds)
                            ->where('items.id', $item->id)
                            ->sum('qty')
                    }}
                    </td>
                    @endforeach
                @endif
        </tr>
        @endforeach
    </tbody>
</table>
