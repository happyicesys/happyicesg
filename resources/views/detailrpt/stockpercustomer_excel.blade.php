@inject('stransactions', 'App\Transaction')
@inject('sprofiles', 'App\Profile')
@inject('sdeals', 'App\Deal')
@inject('speople', 'App\Person')
@inject('scustcategories', 'App\Custcategory')
<meta charset="utf-8">
<table>
    <tbody>
        @if(request())
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
            <th>Overall Sold Qty</th>
            <td data-format="0.00">{{$sdeals::whereIn('id', $dealsIdArr)->sum('qty')}}</td>
        </tr>
        <tr>
            <th>Total Gross Profit</th>
            <td data-format="0.00">
                {{$sdeals::whereIn('id', $dealsIdArr)->sum(DB::raw('ROUND(amount, 2)')) - $sdeals::whereIn('id', $dealsIdArr)->sum(DB::raw('ROUND(unit_cost * qty, 2)'))}}
            </td>
        </tr>

        <tr></tr>
        <tr>
            <th colspan="5"></th>
            <th>Total Sold Qty</th>
            @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
                <th align="center">
                    {{
                        $sdeals::whereIn('id', $dealsIdArr)->whereHas('transaction', function($q) use ($person) {
                            $q->where('person_id', $person->id);
                        })->sum('qty')
                    }}
                </th>
            @endforeach
        </tr>
        <tr>
            <th colspan="5"></th>
            <th>Gross Profit</th>
            @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
                <th align="center">
                    {{
                        $sdeals::whereIn('id', $dealsIdArr)->whereHas('transaction', function($q) use ($person) {
                            $q->where('person_id', $person->id);
                        })->sum('amount')
                    }}
                </th>
            @endforeach
        </tr>
        <tr>
            <th colspan="5"></th>
            <th>Customer Cat</th>
            @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
                <th align="center">
                    {{
                        $speople::where('id', $person->id)->first()->custcategory['name']
                    }}
                </th>
            @endforeach
        </tr>
        <tr>
            <th colspan="5"></th>
            <th>Profile</th>
            @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
                <th align="center">
                    {{
                        $speople::where('id', $person->id)->first()->profile['name']
                    }}
                </th>
            @endforeach
        </tr>
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
            @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
            <th align="center">
                ({{$person->cust_id}}) {{$person->company}}
            </th>
            @endforeach
        </tr>
        @foreach($items as $index => $item)
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
                        ->whereIn('deals.id', $dealsIdArr)
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
                                    ->whereIn('deals.id', $dealsIdArr)
                                    ->where('items.id', $item->id)
                                    ->sum('qty');
                    @endphp
                    @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
                    @php
                        $personTransactionsId = [];
                        $personTransactions = $stransactions::whereIn('id', $transactionsIdArr)->where('person_id', $person->id)->get();
                        foreach($personTransactions as $personTransaction) {
                            array_push($personTransactionsId, $personTransaction->id);
                        }
                        $cfdeduct -= \DB::table('deals')
                                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                        ->whereIn('deals.transaction_id', $personTransactionsId)
                                        ->where('items.id', $item->id)
                                        ->sum('qty');
                    @endphp
                    <td align="right" data-format="0.0000">
                        {{$cfdeduct}}
                    </td>
                    @endforeach
                @else
                    @foreach($speople::whereIn('id', $peopleIdAllArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdAllArr).')'))->get() as $person)
                    @php
                        $personTransactionsId = [];
                        $personTransactions = $stransactions::whereIn('id', $transactionsIdArr)->where('person_id', $person->id)->get();
                        foreach($personTransactions as $personTransaction) {
                            array_push($personTransactionsId, $personTransaction->id);
                        }
                    @endphp
                    <td align="right" data-format="0.0000">
                    {{

                        \DB::table('deals')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->whereIn('deals.transaction_id', $personTransactionsId)
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
