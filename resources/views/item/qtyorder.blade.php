@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="itemOrderqtyController">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>Booked Qty for {{$item->product_id}} : {{$item->name}} ({{$item->qty_order}}) </strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::hidden('item_id', $item->id, ['id'=>'item_id']) !!}

            <div class="table-responsive" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('id')">
                            INV #
                            <span ng-if="search.sortName == 'id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('cust_id')">
                            ID
                            <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            ID Name
                        </th>
                        <th class="col-md-1 text-center">
                            Cust Cat
                        </th>
                        <th class="col-md-1 text-center">
                            Del Postcode
                        </th>
                        <th class="col-md-1 text-center">
                            Status
                        </th>
                        <th class="col-md-1 text-center">
                            {{-- <a href="" ng-click="sortTable('delivery_date')"> --}}
                            Delivery Date
                            {{-- <span ng-if="search.sortName == 'delivery_date' && !search.sortBy" class="fa fa-caret-down"></span> --}}
                            {{-- <span ng-if="search.sortName == 'delivery_date' && search.sortBy" class="fa fa-caret-up"></span> --}}
                        </th>
                        <th class="col-md-1 text-center">
                            Delivered By
                        </th>
                        <th class="col-md-1 text-center">
                            Total Amount
                        </th>
                        <th class="col-md-1 text-center">
                            Total Qty
                        </th>
                        <th class="col-md-1 text-center">
                            Payment
                        </th>
                        <th class="col-md-1 text-center">
                            Last Modified By
                        </th>
                        <th class="col-md-1 text-center">
                            Last Modified Time
                        </th>
                        <th class="col-md-1 text-center">
                            Action
                        </th>
                    </tr>
                    <tbody>
                        <tr ng-repeat="transaction in transactions | orderBy:sortName:sortReverse">
                            <td class="col-md-1 text-center">@{{ $index + 1 }} </td>
                            <td class="col-md-1 text-center">
                                <a href="/transaction/@{{ transaction.id }}/edit">
                                    @{{ transaction.id }}
                                </a>
                            </td>
                            <td class="col-md-1 text-center">@{{ transaction.person.cust_id }} </td>
                            <td class="col-md-1 text-center">
                                <a href="/person/@{{ transaction.person.id }}">
                                    @{{ transaction.person.cust_id[0] == 'D' || transaction.person.cust_id[0] == 'H' ? transaction.person.name : transaction.person.company }}
                                </a>
                            </td>
                            <td class="col-md-1 text-center">@{{ transaction.person.custcategory.name }} </td>
                            <td class="col-md-1 text-center">@{{ transaction.del_postcode }}</td>
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
                            <td class="col-md-1 text-center">@{{ transaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>
                            <td class="col-md-1 text-center">@{{ transaction.driver }}</td>

                            <td class="col-md-1 text-center" ng-if="transaction.gst && transaction.delivery_fee <= 0">@{{ (+(transaction.total * 7/100) + transaction.total * 1) | currency: ""}} </td>
                            <td class="col-md-1 text-center" ng-if="!transaction.gst && transaction.delivery_fee <= 0">@{{ transaction.total | currency: "" }}</td>
                            <td class="col-md-1 text-center" ng-if="transaction.delivery_fee > 0">@{{ (transaction.total/1) + (transaction.delivery_fee/1) | currency: "" }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.total_qty }}</td>
                            {{-- pay status --}}
                            <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.pay_status == 'Owe'">
                                @{{ transaction.pay_status }}
                            </td>
                            <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.pay_status == 'Paid'">
                                @{{ transaction.pay_status }}
                            </td>
                            {{-- pay status ended --}}
                            <td class="col-md-1 text-center">@{{ transaction.updated_by}}</td>
                            <td class="col-md-1 text-center">@{{ transaction.updated_at }}</td>
                            <td class="col-md-1 text-center">
                                {{-- print invoice         --}}
                                <a href="/transaction/download/@{{ transaction.id }}" class="btn btn-primary btn-sm" ng-if="transaction.status != 'Pending' && transaction.status != 'Cancelled'">Print</a>
                                {{-- button view shown when cancelled --}}
                                <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-default" ng-if="transaction.status == 'Cancelled'">View</a>
                            </td>
                        </tr>
                        <tr ng-if="!transactions || transactions.length == 0">
                            <td colspan="18" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="pull-right">
            <a href="/item" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>

<script src="/js/item_qtyorder.js"></script>
@stop