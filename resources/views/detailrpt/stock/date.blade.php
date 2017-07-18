@inject('profiles', 'App\Profile')
@inject('sdeals', 'App\Deal')
@inject('speople', 'App\Person')
@inject('scustcategories', 'App\Custcategory')
@inject('sitems', 'App\Item')

@extends('template')
@section('title')
    {{$DETAILRPT_TITLE}}
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/detailrpt/stock/date"><h1>Stock Sold/ Balance (Date)</h1></a>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        Stock Sold/ Balance (Date)
    </div>

    <div class="panel-body">
        {!! Form::open(['id'=>'submit_form', 'method'=>'POST', 'action'=>['DetailRptController@getStockDate']]) !!}
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
                    {!! Form::select('custcategory_id', [''=>'All'] + $scustcategories::pluck('name', 'id')->all(),
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
                        Total Revenue $:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                            {{
                                number_format($sdeals::whereIn('transaction_id', $allDateTransactionIds)->sum(DB::raw('ROUND(amount, 2)')), 2)
                            }}
                        </strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Qty:
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                            {{
                                number_format($sdeals::whereIn('transaction_id', $allDateTransactionIds)->sum('qty'), 2)
                            }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" id="exportable_stockcustomer" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-1 text-center">
                        Item ID
                    </th>
                    <th class="col-md-2 text-center">
                        Product
                    </th>
                    <th class="col-md-1 text-center">
                        Unit
                    </th>
                    <th class="col-md-1 text-center">
                        Inventory Item
                    </th>
                    <th class="col-md-1 text-right">
                        Total Qty
                    </th>
                    @foreach($sevenDatesArr as $date)
                        <th class="col-md-1 text-center">
                            {{ \Carbon\Carbon::parse($date->delivery_date)->toDateString() }}
                        </th>
                    @endforeach
                </tr>

                <tbody>
                    @foreach($items = $sitems::whereIn('id', $itemsIdArr)->orderBy('product_id')->get() as $index => $item)
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
                                ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                ->whereIn('transactions.id', $allDateTransactionIds)
                                ->where('items.id', $item->id)
                                ->sum('qty'), 4)
                            }}
                        </td>

                        @php
                            $loopeddate = []
                        @endphp
                        {{-- @foreach($speople::whereIn('id', $peopleIdArr)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $peopleIdArr).')'))->get() as $person) --}}
                        @foreach($sevenDatesArr as $date)
                            <td class="col-md-1 text-right">
                                @if(request('stock_status') === 'Balance')
                                    @php
                                        array_push($loopeddate, $date->delivery_date);
                                    @endphp
                                    {{
                                        number_format((\DB::table('deals')
                                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                        ->whereIn('transactions.id', $sevenDateTransactionIds)
                                        ->where('items.id', $item->id)
                                        ->sum('qty')) - (\DB::table('deals')
                                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                        ->whereIn('transactions.id', $sevenDateTransactionIds)
                                        ->whereIn('transactions.delivery_date', $loopeddate)
                                        ->where('items.id', $item->id)
                                        ->sum('qty')), 4)
                                    }}
                                @else
                                    {{
                                        number_format(\DB::table('deals')
                                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                            ->whereIn('transactions.id', $sevenDateTransactionIds)
                                            ->whereDate('transactions.delivery_date', '=', $date->delivery_date)
                                            ->where('items.id', $item->id)
                                            ->sum('qty'), 4)
                                    }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @if(!$items and count($sevenDateTransactionIds) == 0)
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