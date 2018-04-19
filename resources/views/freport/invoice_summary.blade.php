@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')

<div class="panel panel-primary" ng-app="app" ng-controller="invoiceSummaryController" ng-cloak>
    <div class="panel-heading">
        Invoice Summary
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('collection_from', 'Collection From', ['class'=>'control-label search-title']) !!}
                    <datepicker selector="form-control">
                        <div class="input-group">
                            <input
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Collection From"
                                ng-model = "search.collection_from"
                                ng-change = "onCollectionFromChanged(search.collection_from)"
                            />
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('collection_from', search.collection_from)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('collection_from', search.collection_from)"></span>
                        </div>
                    </datepicker>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('collection_to', 'Collection To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker selector="form-control">
                            <input
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Collection To"
                                ng-model = "search.collection_to"
                                ng-change = "onCollectionToChanged(search.collection_to)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('collection_to', search.collection_to)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('collection_to', search.collection_to)"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
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
                        [''=>'All'] +
                        $people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))
                            ->whereActive('Yes')
                            ->where('cust_id', 'NOT LIKE', 'H%')
                            ->whereHas('profile', function($q) {
                                $q->filterUserProfile();
                            })
                            ->filterFranchiseePeople()
                            ->orderBy('cust_id')
                            ->pluck('full', 'id')
                            ->all(),
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
                    {!! Form::label('is_active', 'Active', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('is_active',
                        [''=>'All', 'Yes'=>'Yes', 'No'=>'No'],
                        null,
                        [
                            'class'=>'select form-control',
                            'ng-model'=>'search.is_active',
                            'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>            
        </div>

        <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-12">
                <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
            </div>
{{--             <div class="col-md-7 col-sm-7 col-xs-12">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Grand Total:
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{grand_total | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Tax Total:
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{taxtotal | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Subtotal
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{subtotal | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Total Gross $
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{fixed_total_gross_money | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            Total Gross %
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                            <strong>
                                @{{fixed_total_gross_percent | currency: "": 2}}
                            </strong>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-3 col-sm-3 col-xs-12 pull-right">
                <div class="row">
                    <label for="display_num">Display</label>
                    <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
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

        <div class="table-responsive" id="exportable_invoicesummary" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered">
{{--                 <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Grand Total</td>
                    <td data-tableexport-display="always" class="text-right">@{{grand_total | currency: "": 2}}</td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Tax Total</td>
                    <td data-tableexport-display="always" class="text-right">@{{taxtotal | currency: "": 2}}</td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Subtotal</td>
                    <td data-tableexport-display="always" class="text-right">@{{subtotal | currency: "": 2}}</td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Total Gross $</td>
                    <td data-tableexport-display="always" class="text-right">@{{fixed_total_gross_money | currency: "": 2}}</td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td data-tableexport-display="always">Total Gross %</td>
                    <td data-tableexport-display="always" class="text-right">@{{fixed_total_gross_percent | currency: "": 2}}</td>
                </tr>
                <tr class="hidden" data-tableexport-display="always">
                    <td></td>
                </tr> --}}

                <tr>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        #
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('cust_id')">
                        Customer
                        <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('first_date')">
                        First Inv Date
                        <span ng-if="search.sortName == 'first_date' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'first_date' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('total')">
                        Grand Total
                        <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('taxtotal')">
                        GST
                        <span ng-if="search.sortName == 'taxtotal' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'taxtotal' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('finaltotal')">
                        Total Revenue
                        <span ng-if="search.sortName == 'finaltotal' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'finaltotal' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('cost')">
                        Total Cost $
                        <span ng-if="search.sortName == 'cost' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'cost' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('gross_money')">
                        Gross Earning $
                        <span ng-if="search.sortName == 'gross_money' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'gross_money' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('gross_percent')">
                        Gross Earning %
                        <span ng-if="search.sortName == 'gross_percent' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'gross_percent' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('paid')">
                        Total Paid
                        <span ng-if="search.sortName == 'paid' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'paid' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #DDFDF8">
                        <a href="" ng-click="sortTable('owe')">
                        Total Owe
                        <span ng-if="search.sortName == 'owe' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'owe' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
{{--                     <th class="col-md-1 text-center" style="background-color: #D896FF">
                        <a href="" ng-click="sortTable('vending_monthly_rental')">
                        Monthly Rental
                        <span ng-if="search.sortName == 'vending_monthly_rental' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'vending_monthly_rental' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        <a href="" ng-click="sortTable('vending_profit_sharing')">
                        Profit Sharing
                        <span ng-if="search.sortName == 'vending_profit_sharing' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'vending_profit_sharing' && search.sortBy" class="fa fa-caret-up"></span>
                    </th> --}}
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        <a href="" ng-click="sortTable('sales_qty')">
                        Total Sales Qty
                        <span ng-if="search.sortName == 'sales_qty' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'sales_qty' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        <a href="" ng-click="sortTable('sales_avg_day')">
                        Avg Sales/ Day
                        <span ng-if="search.sortName == 'sales_avg_day' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'sales_avg_day' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        <a href="" ng-click="sortTable('stock_in')">
                        Stock In
                        <span ng-if="search.sortName == 'stock_in' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'stock_in' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center" style="background-color: #D896FF">
                        <a href="" ng-click="sortTable('delta')">
                        Delta
                        <span ng-if="search.sortName == 'delta' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'delta' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>                    
                </tr>

                <tbody>
                    <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="invoice_summary" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{$index + indexFrom}}
                        </td>
                        <td class="col-md-1 text-center">
                            (@{{deal.cust_id}}) @{{deal.company}}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{deal.first_date}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.total | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.taxtotal | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.finaltotal | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.total_cost | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.gross_profit | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.gross_profit_percent | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.paid | currency: "": 2}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{deal.owe | currency: "": 2}}
{{--                         </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.vending_monthly_rental | currency: "": 2}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.vending_profit_sharing}}
                            </span>
                        </td> --}}
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.sales_qty}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.sales_avg_day | currency: "": 2}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.stock_in}}
                            </span>
                        </td>
                        <td class="col-md-1 text-right">
                            <span ng-if="deal.is_vending==1">
                                @{{deal.delta}}
                            </span>
                        </td>                        
                    </tr>

                    <tr ng-if="alldata || alldata.length > 0">
                        <th colspan="2">
                            Average
                        </th>
                        <th class="col-md-1"></th>
                        <th class="col-md-1 text-right">
                            @{{avg_grand_total | currency: "": 2}}
                        </th>
                        <th class="col-md-1"></th>
                        <th class="col-md-1 text-right">
                            @{{avg_finaltotal | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{avg_cost | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{avg_gross_profit | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{avg_gross_profit_percent | currency: "": 2}}
                        </th>
                        <th class="col-md-1"></th>
                        <th class="col-md-1"></th>
{{--                         <th class="col-md-1 text-right">
                            @{{avg_vending_monthly_rental | currency: "": 2}}
                        </th>
                        <th class="col-md-1"></th> --}}
                        <th class="col-md-1 text-right">
                            @{{avg_sales_qty }}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{avg_sales_avg_day | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{avg_stock_in}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{avg_delta}}
                        </th>                        
                    </tr>
                    <tr ng-if="alldata || alldata.length > 0">
                        <th colspan="2">
                            Total
                        </th>
                        <th class="col-md-1"></th>
                        <th class="col-md-1 text-right">
                            @{{total_grand_total | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_taxtotal | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_finaltotal | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_cost | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_gross_profit | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_gross_profit_percent | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_paid | currency: "": 2}}
                        </th>
                        <th class="col-md-1 text-right">
                            @{{total_owe | currency: "": 2}}
                        </th>
                        {{-- <th class="col-md-1"></th> --}}
{{--                         <th class="col-md-1 text-right">
                            @{{total_vending_monthly_rental | currency: "": 2}}
                        </th>
                        <th class="col-md-1"></th> --}}
                        <th class="col-md-1 text-right">
                            @{{total_sales_qty}}
                        </th>
                        <th class="col-md-1"></th>
                        <th class="col-md-1 text-right">
                            @{{total_stock_in}}
                        </th>                        
                        <th class="col-md-1 text-right">
                            @{{total_delta}}
                        </th>
                    </tr>

                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>

                </tbody>
            </table>

            <div>
                  <dir-pagination-controls max-size="5" pagination-id="invoice_summary" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>


    </div>
</div>