@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('users', 'App\User')

<div class="row">
    <div class="form-group col-md-3 col-sm-6 col-xs-12">
        {!! Form::label('invoice', 'Ref #', ['class'=>'control-label search-title']) !!}
        {!! Form::text('invoice', null,
                                        [
                                            'class'=>'form-control input-sm',
                                            'ng-model'=>'searchvend.id',
                                            'ng-change'=>'searchDB()',
                                            'placeholder'=>'Inv Num',
                                            'ng-model-options'=>'{ debounce: 500 }'
                                        ]) !!}
    </div>
    <div class="form-group col-md-3 col-sm-6 col-xs-12">
        {!! Form::label('collection_from', 'Date From', ['class'=>'control-label search-title']) !!}
        <div class="input-group">
            <datepicker>
                <input
                    type = "text"
                    class = "form-control input-sm"
                    placeholder = "Date From"
                    ng-model = "searchvend.collection_from"
                    ng-change = "collectionFromChanged(searchvend.collection_from)"
                />
            </datepicker>
            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('collection_from', searchvend.collection_from)"></span>
            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('collection_from', searchvend.collection_from)"></span>
        </div>
    </div>
    <div class="form-group col-md-3 col-sm-6 col-xs-12">
        {!! Form::label('collection_to', 'Date To', ['class'=>'control-label search-title']) !!}
        <div class="input-group">
            <datepicker>
                <input
                    type = "text"
                    class = "form-control input-sm"
                    placeholder = "Date To"
                    ng-model = "searchvend.collection_to"
                    ng-change = "collectionToChanged(searchvend.collection_to)"
                />
            </datepicker>
            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('collection_to', searchvend.collection_to)"></span>
            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('collection_to', searchvend.collection_to)"></span>
        </div>
    </div>
    <div class="form-group col-md-3 col-sm-6 col-xs-12">
        <div class="row col-md-12 col-sm-12 col-xs-12">
            {!! Form::label('collection_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
        </div>
        <div class="btn-group">
            <a href="" ng-click="onPrevDateClicked()" class="btn btn-default"><i class="fa fa-backward"></i></a>
            <a href="" ng-click="onTodayDateClicked()" class="btn btn-default"><i class="fa fa-circle"></i></a>
            <a href="" ng-click="onNextDateClicked()" class="btn btn-default"><i class="fa fa-forward"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
            <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
        @endif
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12 text-right">
        <div class="row">
            <label for="display_num">Display</label>
            <select ng-model="vendItemsPerPage" name="vendPageNum" ng-init="vendItemsPerPage='20'" ng-change="vendPageNumChanged()">
                <option ng-value="20">20</option>
                <option ng-value="50">50</option>
                <option ng-value="100">100</option>
                <option ng-value="200">200</option>
                <option ng-value="All">All</option>
            </select>
            <label for="display_num2" style="padding-right: 20px">per Page</label>
        </div>
        <div class="row">
            <label class="" style="padding-right:18px;" for="totalnum">Showing @{{allVendData.length}} of @{{totalVendCount}} entries</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top:5px;">
        <label class="control-label">
            <u>Vend Cash</u>
        </label>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{total_vend_amount ? total_vend_amount : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Avg $/ pcs
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{total_sales_pieces ? total_sales_pieces : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Avg pcs/ day
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{avg_pieces_day ? avg_pieces_day : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top:5px;">
        <label class="control-label">
            <u>Qty</u>
        </label>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Stock In:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_stock_in ? total_stock_in : 0 | currency: "": 0}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total Sold:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ total_sold_qty ? total_sold_qty : 0 | currency: "": 0}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Delta:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ difference_stock_sold ? difference_stock_sold : 0 | currency: "": 0}}</strong>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top:5px;">
        <label class="control-label">
            <u>Payment</u>
        </label>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ transactions_total ? transactions_total : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total Paid:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ transactions_paid ? transactions_paid : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-5">
                Total Owe:
            </div>
            <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                <strong>@{{ transactions_owe ? transactions_owe : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>

</div>

    <div class="table-responsive" id="exportableVend" style="padding-top:20px;">
        <table class="table table-list-searchvend table-hover table-bordered">
            {{-- hidden table for excel export --}}
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total $ Collected</td>
                <td data-tableexport-display="always" class="text-right">@{{total_vend_amount | currency: "": 2}}</td>
            </tr>
            <tr class="hidden" data-tableexport-display="always">
                <td></td>
            </tr>
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('ftransaction_id')">
                    Ref #
                    <span ng-if="searchvend.sortName == 'ftransaction_id' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'ftransaction_id' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('collection_datetime')">
                    Date
                    <span ng-if="searchvend.sortName == 'collection_datetime' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'collection_datetime' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    Time
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('digital_clock')">
                    Resettable Clock
                    <span ng-if="searchvend.sortName == 'digital_clock' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'digital_clock' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('analog_clock')">
                    Accumulative Clock
                    <span ng-if="searchvend.sortName == 'analog_clock' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'analog_clock' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('sales')">
                    Sales (pcs)
                    <span ng-if="searchvend.sortName == 'sales' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'sales' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('total')">
                    $ Collected
                    <span ng-if="searchvend.sortName == 'total' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'total' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('taxtotal')">
                    {{$person->gst_rate + 0}}% GST ($)
                    <span ng-if="searchvend.sortName == 'taxtotal' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'taxtotal' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortVendTable('finaltotal')">
                    $ Exclude GST
                    <span ng-if="searchvend.sortName == 'finaltotal' && !searchvend.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="searchvend.sortName == 'finaltotal' && searchvend.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    Avg $/ pc
                </th>
                <th class="col-md-1 text-center">
                    Avg pcs/ day
                </th>
                <th class="col-md-1 text-center">
                    Updated By
                </th>
                <th class="col-md-2 text-center">
                    Remarks
                </th>
                {{-- <th class="col-md-1"></th> --}}
            </tr>
            <tbody>
                <tr dir-paginate="ftransaction in allVendData | itemsPerPage:VendItemsPerPage | orderBy:sortType:sortReverse" total-items="totalVendCount">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        {{-- <a href="/franchisee/@{{ ftransaction.id }}/edit"> --}}
                            @{{ftransaction.user_code}} @{{ftransaction.ftransaction_id}}
                        {{-- </a> --}}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ ftransaction.collection_date }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ ftransaction.collection_time }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ ftransaction.digital_clock }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ ftransaction.analog_clock }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ ftransaction.sales }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ ftransaction.total }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ ftransaction.taxtotal }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ ftransaction.finaltotal }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ ftransaction.avg_sales_piece }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ ftransaction.avg_sales_day }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ ftransaction.updated_by }}
                    </td>
                    <td class="col-md-2 text-left">
                        <textarea name="remarks[@{{ftransaction.id}}]" class="form-control" style='min-width: 160px; align-content: left; font-size: 12px;' rows="2" ng-model="ftransaction.remarks" ng-change="changeRemarks(ftransaction.id, ftransaction.remarks)" ng-model-options="{ debounce: 600 }"></textarea>
                    </td>
{{--                             <td class="col-md-1 text-center">
                        <button class="btn btn-danger btn-sm" ng-click="removeEntry(ftransaction.id)"><i class="fa fa-times"></i></button>
                    </td> --}}
                </tr>
                <tr ng-if="!allVendData || allVendData.length == 0">
                    <td colspan="18" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="vendPageChanged(newVendPageNumber)"> </dir-pagination-controls>
    </div>