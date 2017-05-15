@inject('profiles', 'App\Profile')
@inject('searchpeople', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('searchtransactions', 'App\Transaction')
@inject('searchdeals', 'App\Deal')

@extends('template')
@section('title')
	{{$DETAILRPT_TITLE}}
@stop
@section('content')

<div class="row">
	<a class="title_hyper pull-left" href="/detailrpt/invbreakdown/summary"><h1>Invoice Breakdown - Summary</h1></a>
</div>

<div class="panel panel-primary" ng-app="app" ng-controller="invbreakdownSummaryController" ng-cloak>
    <div class="panel-heading">
        Invoice Breakdown Summary
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('profile_id', [''=>'All']+$profiles::lists('name', 'id')->all(), null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.profile_id',
                            'ng-change'=>'searchDB()',
                            'ng-model-options'=>'{ debounce: 500 }'
                        ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                    <datepicker selector="form-control">
                        <div class="input-group">
                            <input
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Delivery From"
                                ng-model = "search.delivery_from"
                                ng-change = "onDeliveryFromChanged(search.delivery_from)"
                            />
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span>
                        </div>
                    </datepicker>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker selector="form-control">
                            <input
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Delivery To"
                                ng-model = "search.delivery_to"
                                ng-change = "onDeliveryToChanged(search.delivery_to)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_to', search.delivery_to)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_to', search.delivery_to)"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.status',
                            'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('cust_id',
                        null,
                        [
                            'class'=>'form-control',
                            'ng-model'=>'search.cust_id',
                            'placeholder'=>'Cust ID',
                            'ng-change'=>'searchDB()',
                            'ng-model-options'=>'{ debounce: 500 }'
                        ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company',
                        null,
                        [
                            'class'=>'form-control',
                            'ng-model'=>'search.company',
                            'placeholder'=>'ID Name',
                            'ng-change'=>'searchDB()',
                            'ng-model-options'=>'{ debounce: 500 }'
                        ])
                    !!}
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id',
                        [''=>'All'] + $searchpeople::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                        null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.person_id',
                            'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('custcategory', [''=>'All'] + $custcategories::pluck('name', 'id')->all(),
                        null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.custcategory',
                            'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Revenue ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                        </strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Total Gross Earning ($):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                        </strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        Overall Gross Earning (%):
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                        <strong>
                        </strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 text-right">
                <div class="row">
                    <label for="display_num">Display</label>
                    <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                        <option ng-value="100">100</option>
                        <option ng-value="200">200</option>
                        <option ng-value="All">All</option>
                    </select>
                    <label for="display_num2" style="padding-right: 20px">per Page</label>
                </div>
                <div class="row">
                    <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                </div>
            </div>
        </div>

        <div class="table-responsive" id="exportable_invbreakdownsummary" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Total Amount</td>
                    <td data-tableexport-display="always" class="text-right">@{{total_amount | currency: "": 2}}</td>
                </tr>
                <tr class="hidden" data-tableexport-display="always">
                    <td></td>
                </tr>

                <tr>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        #
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Customer
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Customer Cat
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        First Inv Date
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Total Revenue
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        GST
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Subtotal Revenue
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Total Cost $
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Gross Earning $
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Gross Earning %
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Total Paid
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        Total Owe
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        Price Per Piece
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        Monthly Rental
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        Profit Sharing
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        Total Sales Qty
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        Avg Sales/ Day
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        Difference (Actual - Expected)
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        VM Stock Value
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="cust_detail" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{$index + indexFrom}}
                        </td>
                        <td class="col-md-1 text-center">
                            (@{{deal.cust_id}}) @{{deal.company}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{deal.custcategory_name}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{deal.first_date}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.total}}
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.gst==1">
                                @{{deal.gsttotal}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.subtotal}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.cost}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.gross_money}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.gross_percent}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.paid}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.owe}}
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.vending_piece_price}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.vending_monthly_rental}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.vending_profit_sharing}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.sales_qty}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.sales_avg_day}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.is_vending}}
                            </span>
                        </td>
{{--                         <td class="col-md-1 text-center">
                            {{$index + 1}}
                        </td>
                        <td class="col-md-1 text-center">
                            ({{$person->cust_id}}) {{$person->company}}
                        </td>
                        <td class="col-md-1 text-center">
                            {{$person->custcategory->name}}
                        </td>
                        <td class="col-md-1 text-center">
                            {{$searchtransactions::wherePersonId($person->id)->oldest()->first()->delivery_date}}
                        </td>

                        <td class="col-md-1 text-right">
                            {{number_format($peopledeals->sum('amount'), 2, '.', '')}}
                        </td>
                        <td class="col-md-1 text-right">
                            {{number_format($peopledeals->sum('unit_cost'), 2, '.', '')}}
                        </td>
                        <td class="col-md-1 text-right">
                            {{number_format(($peopledeals->sum('amount') -  $peopledeals->sum('unit_cost')), 2, '.', '')}}
                        </td>
                        <td class="col-md-1 text-right">
                            @if($peopledeals->sum('amount') != 0)
                                {{number_format(($peopledeals->sum('amount') -  $peopledeals->sum('unit_cost'))/ $peopledeals->sum('amount') * 100, '2', '.', '')}}
                            @else
                                {{number_format(($peopledeals->sum('amount') -  $peopledeals->sum('unit_cost')), '2', '.', '')}}
                            @endif
                        </td>
                        <td class="col-md-1 text-right">
                            {{number_format(($peopletransac_paid->wherePayStatus('Paid')->sum('total')), 2, '.', '')}}
                        </td>
                        <td class="col-md-1 text-right">
                            {{number_format(($peopletransac_owe->wherePayStatus('Owe')->sum('total')), 2, '.', '')}}
                        </td>
                        @if($person->is_vending === 1)
                            <td class="col-md-1 text-right">
                                {{$person->vending_piece_price}}
                            </td>
                            <td class="col-md-1 text-right">
                                {{$person->vending_monthly_rental}}
                            </td>
                            <td class="col-md-1 text-right">
                                {{$person->vending_profit_sharing}}
                            </td>
                        @endif --}}
                    </tr>

                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>

                </tbody>
            </table>

            <div>
                  <dir-pagination-controls max-size="5" pagination-id="invbreakdown_summary" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>


    </div>
</div>

<script src="/js/invbreakdown_summary.js"></script>
@stop