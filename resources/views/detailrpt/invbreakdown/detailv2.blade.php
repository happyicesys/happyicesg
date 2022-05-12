@inject('custcategories', 'App\Custcategory')
@inject('custcategoryGroups', 'App\CustcategoryGroup')
@inject('people', 'App\Person')
@inject('items', 'App\Item')
@inject('persontags', 'App\Persontag')
@inject('transactions', 'App\Transaction')

@extends('template')
@section('title')
    {{$DETAILRPT_TITLE}}
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/detailrpt/invbreakdown/detail/v2"><h1>Invoice Breakdown - Detail v2</h1></a>
</div>

<div class="panel panel-primary" ng-app="app" ng-controller="invbreakdownDetailv2Controller" ng-cloak>
    <div class="panel-heading">
        Invoice Breakdown v2
    </div>

    <div class="panel-body">

        {!! Form::open(['id'=>'submit_invoicebreakdown', 'method'=>'POST', 'action'=>['DetailRptController@getInvoiceBreakdownDetailv2']]) !!}
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('custcategories', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    <label class="pull-right">
                        <input type="checkbox" ng-model="search.excludeCustCat" ng-change="searchDB()" ng-model-options="{debounce: 500}">
                        <span style="margin-top: 5px; margin-right: 5px; font-size: 12px;">
                            Exclude
                        </span>
                    </label>
                    <select ng-model="search.custcategories" class="selectmultiple form-control" multiple  ng-change="searchDB()">
                        @foreach($custcategories::orderBy('name')->get() as $custcategory)
                            <option value="{{$custcategory->id}}" {{in_array($custcategory->id, $request->custcategories) ? 'selected' : ''}}>
                                {{$custcategory->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('custcategoryGroups', 'CustCategory Group', ['class'=>'control-label search-title']) !!}
                    <select ng-model="search.custcategoryGroups" class="selectmultiple form-control" multiple ng-change="searchDB()">
                        @foreach($custcategoryGroups::orderBy('name')->get() as $custcategoryGroup)
                            <option value="{{$custcategoryGroup->id}}" {{in_array($custcategoryGroup->id, $request->custcategoryGroups) ? 'selected' : ''}}>
                                {{$custcategoryGroup->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('active', 'Cust Status', ['class'=>'control-label search-title']) !!}
                    <select ng-model="search.actives" id="active" class="selectmultiple form-control" multiple ng-change="searchDB()">
                        <option value="Potential" {{in_array('Potential', $request->actives) ? 'selected' : ''}}>Potential</option>
                        <option value="New" {{in_array('New', $request->actives) ? 'selected' : ''}}>New</option>
                        <option value="Yes" {{in_array('Yes', $request->actives) ? 'selected' : ''}}>Active</option>
                        <option value="Pending" {{in_array('Pending', $request->actives) ? 'selected' : ''}}>Pending</option>
                        <option value="No" {{in_array('No', $request->actives) ? 'selected' : ''}}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('statuses', 'Status', ['class'=>'control-label search-title']) !!}
                    <select name="statuses" class="selectmultiple form-control" multiple ng-model="search.statuses" ng-change="searchDB()">
                        <option value="Pending" {{in_array('Pending', $request->statuses) ? 'selected' : ''}}>Pending</option>
                        <option value="Confirmed" {{in_array('Confirmed', $request->statuses) ? 'selected' : ''}}>Confirmed</option>
                        <option value="Delivered" {{in_array('Delivered', $request->statuses) ? 'selected' : ''}}>Delivered</option>
                        <option value="Cancelled" {{in_array('Cancelled', $request->statuses) ? 'selected' : ''}}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_from', 'Delivery From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "delivery_from"
                                type = "text"
                                class = "form-control"
                                placeholder = "Delivery From"
                                ng-model = "search.delivery_from"
                                ng-change = "onDeliveryFromChanged(search.delivery_from)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "delivery_to"
                                type = "text"
                                class = "form-control"
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
                    <div class="row col-md-12 col-sm-12 col-xs-12">
                        {!! Form::label('delivery_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
                    </div>
                    <div class="btn-group">
                        <a href="" ng-click="onPrevDateClicked('delivery_from', 'delivery_to')" class="btn btn-default"><i class="fa fa-backward"></i></a>
                        <a href="" ng-click="onTodayDateClicked('delivery_from', 'delivery_to')" class="btn btn-default"><i class="fa fa-circle"></i></a>
                        <a href="" ng-click="onNextDateClicked('delivery_from', 'delivery_to')" class="btn btn-default"><i class="fa fa-forward"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('personTags', 'Tags', ['class'=>'control-label search-title']) !!}
                    <select name="personTags" class="selectmultiple form-control" multiple ng-model="search.personTags" ng-change="searchDB()">
                        @foreach($persontags::orderBy('name')->get() as $persontag)
                            <option value="{{$persontag->id}}" {{in_array($persontag->id, $request->personTags) ? 'selected' : ''}}>
                                {{$persontag->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="btn-group">
                    <button class="btn btn-success" ng-click="onSearchButtonClicked($event)">
                        Search
                        <i class="fa fa-search" ng-show="!spinner"></i>
                        <i class="fa fa-spinner fa-1x fa-spin" ng-show="spinner"></i>
                    </button>
                    <button class="btn btn-primary" ng-click="exportData($event)">Export Excel</button>
                    {{-- <button type="submit" class="btn btn-warning">Export Profile Excel</button> --}}
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <div id="exportable_invbreakdownDetailv2">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-5 col-sm-5 col-xs-12">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Total Revenue ($):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>@{{totals['amount'] ? totals['amount'] : 0.00 | currency: "": 2}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Total Ice Cream Cost ($):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>@{{totals['cost'] ? totals['cost'] : 0.00 | currency: "": 2}}</strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Gross Earning ($):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>
                                    @{{totals['gross'] ? totals['gross'] : 0.00 | currency: "": 2}}
                                </strong>
                            </div>
                        </div>
                        <div class="row" ng-if="totals['gross'] > 0">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                Gross Earning (%):
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                                <strong>
                                    @{{totals['amount'] ? (totals['gross']/ totals['amount'] * 100) : 0.00 | currency: "": 0}}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="padding-top: 20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-6 text-center">Item</th>
                        <th class="col-md-2 text-center">Total Qty</th>
                        <th class="col-md-2 text-center">Total $</th>
                    </tr>

                    <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="invbreakdown_detailv2" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-6">
                            @{{deal.product_id}} - @{{deal.name}}
                        </td>
                        <td class="col-md-2 text-right">
                            @{{deal.qty | currency: "": 4}}
                        </td>
                        <td class="col-md-2 text-right">
                            @{{deal.amount | currency: "": 2}}
                        </td>
                    </tr>

                    <div ng-if="alldata || alldata.length > 0">
                        <tr>
                            <th class="col-md-4">Total</th>
                            <th class="col-md-1 text-right">
                                <strong>@{{totals['qty'] ? totals['qty'] : 0.0000 | currency: "": 4}}</strong>
                            </th>
                            <th class="col-md-1 text-right">
                                <strong>@{{totals['amount'] ? totals['amount'] : 0.00 | currency: "": 2}}</strong>
                            </th>

                        </tr>
                    </div>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No results found</td>
                    </tr>
                </table>
                <div>
                    {{-- <dir-pagination-controls max-size="5" pagination-id="invbreakdown_detailv2" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/invbreakdown_detailv2.js"></script>
@stop