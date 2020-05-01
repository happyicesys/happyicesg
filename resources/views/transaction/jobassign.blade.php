@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')
@inject('persontags', 'App\Persontag')
@inject('users', 'App\User')

@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')

    <div ng-app="app" ng-controller="transController">

    <div class="row">
        <a class="title_hyper pull-left" href="/transaction"><h1> Job Assign <i class="fa fa-paper-plane" aria-hidden="true"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

    <div class="panel panel-default" ng-cloak>
        <div class="panel-body">
            {!! Form::open(['id'=>'transaction_rpt', 'method'=>'POST','action'=>['TransactionController@exportAccConsolidatePdf']]) !!}
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('delivery_from', 'Delivery Date', ['class'=>'control-label search-title']) !!}
                        <div class="form-group">
                            <datepicker>
                                <input
                                    name = "delivery_from"
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Delivery Date"
                                    ng-model = "search.delivery_from"
                                    ng-change = "dateChange('delivery_from', search.delivery_from)"
                                />
                            </datepicker>
                            {{-- <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_from', search.delivery_from)"></span> --}}
                            {{-- <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_from', search.delivery_from)"></span> --}}
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12 hidden">
                        {!! Form::label('delivery_to', 'Delivery To', ['class'=>'control-label search-title']) !!}
                        <div class="input-group">
                            <datepicker>
                                <input
                                    name = "delivery_to"
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Delivery To"
                                    ng-model = "search.delivery_to"
                                    ng-change = "dateChange('delivery_to', search.delivery_to)"
                                />
                            </datepicker>
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('delivery_to', search.delivery_to)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('delivery_to', search.delivery_to)"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        <div class="row col-md-12 col-sm-12 col-xs-12">
                            {!! Form::label('delivery_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
                        </div>
                        <div class="btn-group">
                            <a href="" ng-click="onPrevDateClicked('delivery_from', 'delivery_to')" class="btn btn-default btn-sm"><i class="fa fa-backward"></i></a>
                            <a href="" ng-click="onTodayDateClicked('delivery_from', 'delivery_to')" class="btn btn-default btn-sm"><i class="fa fa-circle"></i></a>
                            <a href="" ng-click="onNextDateClicked('delivery_from', 'delivery_to')" class="btn btn-default btn-sm"><i class="fa fa-forward"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <button class="btn btn-sm btn-primary" ng-click="exportData($event)">Export Excel</button>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()" ng-if="alldata.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
                        @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <button class="btn btn-sm btn-default" ng-click="onDriverAssignToggleClicked($event)">
                            <span ng-if="driverOptionShowing === true">
                                Hide Map & Driver Assign
                            </span>
                            <span ng-if="driverOptionShowing === false">
                                Show Map & Driver Assign
                            </span>
                        </button>

                        <button class="btn btn-sm btn-primary" ng-click="onBatchFunctionClicked($event)">
                            Batch Function
                            <span ng-if="!showBatchFunctionPanel" class="fa fa-caret-down"></span>
                            <span ng-if="showBatchFunctionPanel" class="fa fa-caret-up"></span>
                        </button>
                        @endif
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top:5px;">
                        <div class="col-md-5 col-xs-5">
                            Total
                        </div>
                        <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                            <strong>@{{total_amount ? total_amount : 0.00 | currency: "": 2}}</strong>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                        <div class="row">
                            <label for="display_num">Display</label>
                            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='200'" ng-change="pageNumChanged()">
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
                <div ng-show="showBatchFunctionPanel">
                    <hr class="row">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('batch_assign_driver', 'Batch Assign Driver', ['class'=>'control-label search-title']) !!}
                                    <select name="driver" class="form-control select" ng-model="form.driver">
                                        <option value="-1">
                                            -- Clear --
                                        </option>
                                        @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                                            @if(($user->hasRole('driver') or $user->hasRole('technician')) and count($user->profiles) > 0)
                                                <option value="{{$user->name}}">
                                                    {{$user->name}}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                <label class="control-label"></label>
                                <div class="btn-group-control">
                                    <button type="submit" class="btn btn-sm btn-warning" name="batch_assign" value="invoice" ng-click="onBatchAssignDriverClicked($event)"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Batch Assign</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('batch_change_delivery_date', 'Batch Change Delivery Date', ['class'=>'control-label search-title']) !!}
                                    <datepicker>
                                        <input
                                            name = "delivery_date"
                                            type = "text"
                                            class = "form-control input-sm"
                                            placeholder = "Delivery Date"
                                            ng-model = "form.delivery_date"
                                            ng-change = "formDeliveryDateChange('delivery_date', form.delivery_date)"
                                        />
                                    </datepicker>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                <label class="control-label"></label>
                                <div class="btn-group-control">
                                    <button type="submit" class="btn btn-sm btn-warning" name="batch_change_delivery_date" value="invoice" ng-click="onBatchChangeDeliveryDateClicked($event)"><i class="fa fa-calendar" aria-hidden="true"></i> Batch Change Delivery Date</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="row">
                </div>
                {!! Form::close() !!}
                <div class="table-responsive" id="exportable" style="padding-top:20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        {{-- hidden table for excel export --}}
                        <tr class="hidden">
                            <td></td>
                            <td data-tableexport-display="always">Total Amount</td>
                            <td data-tableexport-display="always" class="text-right">@{{total_amount | currency: "": 2}}</td>
                        </tr>
                        <tr class="hidden" data-tableexport-display="always">
                            <td></td>
                        </tr>
                        <tr style="background-color: #DDFDF8">

                            <th class="col-md-1 text-center">
                                {{-- <input type="checkbox" id="checkAll" /> --}}
                                <input type="checkbox" id="check_all" ng-model="form.checkall" ng-change="onCheckAllChecked()"/>
                            </th>
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.id')">
                                INV #
                                <span ng-if="search.sortName == 'transactions.id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @if(!auth()->user()->hasRole('hd_user'))

                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('cust_id')">
                                ID
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('company')">
                                ID Name
                                <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('custcategories.name')">
                                Cust Cat
                                <span ng-if="search.sortName == 'custcategories.name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'custcategories.name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.del_postcode')">
                                Del Postcode
                                <span ng-if="search.sortName == 'transactions.del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Zone
                            </th>
                            @else

                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('do_po')">
                                PO Num
                                <span ng-if="search.sortName == 'do_po' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'do_po' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('requester_name')">
                                Requester Name
                                <span ng-if="search.sortName == 'requester_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'requester_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('pickup_location_name')">
                                Pickup Loc Name
                                <span ng-if="search.sortName == 'pickup_location_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'pickup_location_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('delivery_location_name')">
                                Delivery Loc Name
                                <span ng-if="search.sortName == 'delivery_location_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'delivery_location_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @endif

                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('status')">
                                Status
                                <span ng-if="search.sortName == 'status' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'status' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.po_no')">
                                PO Num
                                <span ng-if="search.sortName == 'transactions.po_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.po_no' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.name')">
                                Attn Name
                                <span ng-if="search.sortName == 'transactions.name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.contact')">
                                Contact
                                <span ng-if="search.sortName == 'transactions.contact' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.contact' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @if(!auth()->user()->hasRole('hd_user'))
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('delivery_date')">
                                Delivery Date
                                <span ng-if="search.sortName == 'delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('driver')">
                                Assigned Driver
                                <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @else
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('delivery_date1')">
                                Requested Delivery Date
                                <span ng-if="search.sortName == 'delivery_date1' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'delivery_date1' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @endif
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('total')">
                                Total Amount
                                <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @if(!auth()->user()->hasRole('hd_user'))
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('total_qty')">
                                Total Qty
                                <span ng-if="search.sortName == 'total_qty' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'total_qty' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            @endif
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('pay_status')">
                                Payment
                                <span ng-if="search.sortName == 'pay_status' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'pay_status' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('del_address')">
                                Del Address
                                <span ng-if="search.sortName == 'del_address' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'del_address' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('updated_by')">
                                Last Modified By
                                <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('transactions.updated_at')">
                                Last Modified Time
                                <span ng-if="search.sortName == 'transactions.updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'transactions.updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">

                                <td class="col-md-1 text-center">
                                    <input type="checkbox" name="checkbox" ng-model="transaction.check">
                                </td>
                                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                <td class="col-md-1 text-center">
                                    <a href="/transaction/@{{ transaction.id }}/edit">
                                        @{{ transaction.id }}
                                    </a>
                                    <i class="fa fa-flag" aria-hidden="true" style="color:red; cursor:pointer;" ng-if="transaction.is_important" ng-click="onIsImportantClicked(transaction.id, $index)"></i>
                                    <i class="fa fa-flag" aria-hidden="true" style="color:grey; cursor:pointer;" ng-if="!transaction.is_important" ng-click="onIsImportantClicked(transaction.id, $index)"></i>
                                </td>

                                @if(!auth()->user()->hasRole('hd_user'))
                                <td class="col-md-1 text-center">@{{ transaction.cust_id }} </td>
                                <td class="col-md-1 text-center">
                                    <a href="/person/@{{ transaction.person_id }}">
                                        @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                                    </a>
                                </td>
                                <td class="col-md-1 text-center">
                                    {{-- print invoice         --}}
                                    <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                    {{-- button view shown when cancelled --}}
                                    <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(transaction, $index)" ng-if="driverOptionShowing"><i class="fa fa-map-o"></i> Map</button>
                                </td>
                                <td class="col-md-1 text-center">@{{ transaction.custcategory }} </td>
                                <td class="col-md-1 text-center">@{{ transaction.del_postcode }}</td>
                                <td class="col-md-1 text-left">
                                    <span ng-if="transaction.west == 1">West</span>
                                    <span ng-if="transaction.east == 1">East</span>
                                    <span ng-if="transaction.north == 1">North</span>
                                    <span ng-if="transaction.others == 1">Others</span>
                                    <span ng-if="transaction.sup == 1">Sup</span>
                                    <span ng-if="transaction.ops == 1">Ops</span>
                                </td>
                                @else
                                <td class="col-md-1 text-center">@{{ transaction.do_po }} </td>
                                <td class="col-md-1 text-center">@{{ transaction.requester_name }} </td>
                                <td class="col-md-1 text-center">@{{ transaction.pickup_location_name }} </td>
                                <td class="col-md-1 text-center">@{{ transaction.delivery_location_name }}</td>
                                @endif

                                {{-- status by color --}}
                                <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Pending'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: orange;" ng-if="transaction.status == 'Confirmed'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.status == 'Delivered'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: black; background-color:orange;" ng-if="transaction.status == 'Verified Owe'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: black; background-color:green;" ng-if="transaction.status == 'Verified Paid'">
                                    @{{ transaction.status }}
                                </td>
                                <td class="col-md-1 text-center" ng-if="transaction.status == 'Cancelled'">
                                    <span style="color: white; background-color: red;" > @{{ transaction.status }} </span>
                                </td>
                                {{-- status by color ended --}}
                                <td class="col-md-1 text-center">@{{ transaction.po_no}}</td>
                                <td class="col-md-1 text-center">@{{ transaction.name}}</td>
                                <td class="col-md-1 text-center">@{{ transaction.contact}}</td>
                                @if(!auth()->user()->hasRole('hd_user'))
                                <td class="col-md-1 text-center">@{{ transaction.del_date}}</td>
                                <td class="col-md-1 text-center">
                                    @{{ transaction.driver }}
                                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                    <ui-select ng-model="transaction.driverchosen" on-select="onFormDriverChanged(transaction, $index)" ng-if="driverOptionShowing">
                                        <ui-select-match allow-clear="true">
                                            <span ng-bind="$select.driver.name"></span>
                                        </ui-select-match>
                                        <ui-select-choices null-option="removeDriver" repeat="user in users | filter: $select.search">
                                            <div ng-bind-html="user.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                    @endif
                                </td>
                                @else
                                <td class="col-md-1 text-center">@{{ transaction.delivery_date1}}</td>
                                @endif
                                <td class="col-md-1 text-right">
                                    @{{ transaction.total | currency: "": 2}}
                                </td>
                                @if(!auth()->user()->hasRole('hd_user'))
                                <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                                @endif
                                {{-- pay status --}}
                                <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                    @{{ transaction.pay_status }}
                                </td>
                                <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                    @{{ transaction.pay_status }}
                                </td>
                                {{-- pay status ended --}}
                                <td class="col-md-1 text-center">@{{ transaction.del_address}}</td>
                                <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                                <td class="col-md-1 text-center">@{{ transaction.updated_at }}</td>
                            </tr>
                            <tr ng-if="!alldata || alldata.length == 0">
                                <td colspan="24" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                </div>

    </div>

    <div id="mapModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Plotted Map</h4>
                </div>
                <div class="modal-body">
                    <div id="map"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <script src="/js/jobassign.js"></script>
    <script>
        $('#delfrom').datetimepicker({
            format: 'DD-MMMM-YYYY'
        });

        // $('.select').select2({});
    </script>
@stop