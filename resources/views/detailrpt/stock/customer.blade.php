@inject('profiles', 'App\Profile')
@inject('sdeals', 'App\Deal')
@inject('speople', 'App\Person')
@inject('scustcategories', 'App\Custcategory')

@extends('template')
@section('title')
	{{$DETAILRPT_TITLE}}
@stop
@section('content')

<div class="row">
	<a class="title_hyper pull-left" href="/detailrpt/stock/customer"><h1>Stock Per Customer</h1></a>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        Stock Per Customer
    </div>

    <div class="panel-body">
        {!! Form::open(['id'=>'submit_form', 'method'=>'POST', 'action'=>['DetailRptController@getStockPerCustomer']]) !!}
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('profile_id', [''=>'All']+
                        $profiles::filterUserProfile()
                            ->pluck('name', 'id')
                            ->all(),
                        request('profile_id') ? request('profile_id') : null,
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_from',
                        request('delivery_from') ? request('delivery_from') : \Carbon\Carbon::today()->startOfMonth()->toDateString(),
                        ['class'=>'form-control', 'id'=>'delivery_from'])
                        !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_to',
                        request('delivery_to') ? request('delivery_to') : \Carbon\Carbon::today()->toDateString(),
                        ['class'=>'form-control', 'id'=>'delivery_to'])
                        !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('stock_status', 'Status', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('stock_status',
                        ['Sold'=>'Sold', 'Balance'=>'Balance'],
                        request('stock_status') ? request('stock_status') : 'Sold',
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('cust_id',
                        request('cust_id') ? request('cust_id') : null,
                        ['class'=>'form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company',
                        request('company') ? request('company') : null,
                        ['class'=>'form-control'])
                    !!}
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id',
                        [''=>'All'] +
                        $speople::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))
                            ->whereActive('Yes')
                            ->where('cust_id', 'NOT LIKE', 'H%')
                            ->whereHas('profile', function($q) {
                                $q->filterUserProfile();
                            })
                            ->orderBy('cust_id')
                            ->pluck('full', 'id')
                            ->all(),
                        request('person_id') ? request('person_id') : null,
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('custcategory_id', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('custcategory_id', [''=>'All'] + $scustcategories::orderBy('name')->pluck('name', 'id')->all(),
                        request('custcategory_id') ? request('custcategory_id') : null,
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('is_inventory', 'Product Type', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('is_inventory', ['1'=>'Inventory Item', 'All'=>'All'],
                        request('is_inventory') ? request('is_inventory') : '1',
                        ['class'=>'select form-control'])
                    !!}
                </div>
            </div>
        </div>
        {!! Form::close() !!}

        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12">
                <button type="submit" class="btn btn-default" form="submit_form">
                    <i class="fa fa-search"></i><span class="hidden-xs"> Search</span>
                </button>
                <button type="submit" class="btn btn-primary" name="export_excel" value="export_excel" form="submit_form">
                <i class="fa fa-file-excel-o"></i><span class="hidden-xs"> Export All Excel</span>
                </button>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Overall Sold Qty:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                            {{
                                number_format($sdeals::whereIn('id', $dealsIdArr)->sum('qty'), 4)
                            }}
                        </strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Gross Profit:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                            {{
                                number_format($sdeals::whereIn('id', $dealsIdArr)->sum(DB::raw('ROUND(amount, 2)')) - $sdeals::whereIn('id', $dealsIdArr)->sum(DB::raw('ROUND(unit_cost * qty, 2)')), 2)
                            }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" id="exportable_stockcustomer" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Overall Sold Qty</td>
                    <td data-tableexport-display="always" class="text-right">@{{total_sold_qty | currency: "": 2}}</td>
                    <td></td>
                    <td data-tableexport-display="always">Total Gross Profit</td>
                    <td data-tableexport-display="always" class="text-right">@{{total_gross_profit | currency: "": 2}}</td>
                </tr>
                <tr class="hidden" data-tableexport-display="always">
                    <td></td>
                </tr>
                @php
                    // $speople = $speople::whereIn('id', $peopleIdArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdArr).')'))->get();
                    $speople = $speople::whereIn('id', $peopleIdArr)->orderBy('cust_id')->get();
                    // dd($speople->toArray());
                @endphp
                <tr>
                    <th colspan="5"></th>
                    <th class="text-center">Total Sold Qty</th>
                    @foreach($speople as $person)
                        <td class="text-center">
                            {{
                                number_format($sdeals::whereIn('id', $dealsIdArr)->whereHas('transaction', function($q) use ($person) {
                                    $q->where('person_id', $person->id);
                                })->sum('qty'), 4)
                            }}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th colspan="5"></th>
                    <th class="text-center">Gross Profit</th>
                    @foreach($speople as $person)
                        <td class="text-center">
                             {{
                                number_format($sdeals::whereIn('id', $dealsIdArr)->whereHas('transaction', function($q) use ($person) {
                                    $q->where('person_id', $person->id);
                                })->sum('amount'), 2)
                            }}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th colspan="5"></th>
                    <th class="text-center">Customer Cat</th>
                    @foreach($speople as $person)
                        <td class="text-center">
                            {{$person->custcategory->name}}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th colspan="5"></th>
                    <th class="text-center">Profile</th>
                    @foreach($speople as $person)
                        <td class="text-center">
                            {{$person->profile->name}}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        #
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Item ID
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Product
                    </th>
                    <th class="col-md-2 text-center" style="background-color: #DDFDF8">
                        Unit
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Inventory Item
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Total Qty
                    </th>
                    @foreach($speople as $person)
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        ({{$person->cust_id}}) {{$person->company}}
                    </th>
                    @endforeach
                </tr>

                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td class="col-md-1 text-center">
                            {{$index + 1}}
                        </td>
                        <td class="col-md-1 text-center">
                            {{$item->product_id}}
                        </td>
                        <td class="col-md-2 text-left">
                            {{$item->name}}
                        </td>
                        <td class="col-md-1 text-center">
                            {{$item->unit}}
                        </td>
                        <td class="col-md-1 text-center">
                            {{$item->is_inventory ? 'Yes' : 'No'}}
                        </td>
                        <td class="col-md-1 text-right">
                                {{
                                    number_format(\DB::table('deals')
                                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                    ->whereIn('deals.id', $dealsIdArr)
                                    ->where('items.id', $item->id)
                                    ->sum('qty'), 4)
                                }}
                        </td>

                        @php
                            $loopedperson = []
                        @endphp
                        @foreach($speople as $person)
                        <td class="col-md-1 text-right">

                            @if(request('stock_status') === 'Balance')
                                @php
                                    array_push($loopedperson, $person->id);
                                @endphp
                                {{
                                    number_format((\DB::table('deals')
                                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                    ->whereIn('deals.id', $dealsIdArr)
                                    ->where('items.id', $item->id)
                                    ->sum('qty')) - (\DB::table('deals')
                                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                    ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                                    ->whereIn('deals.id', $dealsIdArr)
                                    ->whereIn('people.id', $loopedperson)
                                    ->where('items.id', $item->id)
                                    ->sum('qty')), 4)
                                }}
                            @else
                                {{
                                    number_format(\DB::table('deals')
                                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                        ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                                        ->whereIn('deals.id', $dealsIdArr)
                                        ->where('people.id', $person->id)
                                        ->where('items.id', $item->id)
                                        ->sum('qty'), 4)
                                }}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @if(!$items and count($speople) == 0)
                    <tr>
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>


    </div>
</div>

<script>
    $('.date').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('.select').select2();
</script>
@stop