@inject('people', 'App\Person')

<div class="col-md-12">
    <div class="panel panel-success row">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title">
                    @unless($transaction->status == 'Cancelled' or $transaction->status == 'Deleted')
                    <h3 class="panel-title"><strong>Selected : {{$person->cust_id}} - {{$person->company}} ({{$person->name}})</strong></h3>
                    @else
                    <h3 class="panel-title"><strong><del>Selected : {{$person->cust_id}} - {{$person->company}} ({{$person->name}})</del></strong></h3>
                    @endunless
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            Item Code
                        </th>
                        <th class="col-md-4 text-center">
                            Description
                        </th>
                        <th class="col-md-2 text-center">
                            Quantity
                        </th>
                        <th class="col-md-1 text-center">
                            Unit Price
                        </th>
                        <th class="col-md-1 text-center">
                            Amount
                        </th>
                        <th class="col-md-1 text-center">
                            Action
                        </th>
                    </tr>

                    <tbody>
                        <tr ng-repeat="deal in deals">
                            <td class="col-md-1 text-center">@{{ $index + 1 }}</td>
                            <td class="col-md-1 text-center">@{{ deal.item.product_id }}</td>
                            <td class="col-md-5">@{{ deal.item.name }} @{{ deal.item.remark }}</td>
{{--
                            <td class="col-md-2 text-right" ng-if="deal.qty % 1 == 0 ">@{{ Math.round(deal.qty) }} @{{ deal.item.unit }}</td>
                            <td class="col-md-2 text-right" ng-if="deal.qty % 1 != 0">@{{ deal.qty }} @{{ deal.item.unit}}</td> --}}
{{--
                            <td class="col-md-2 text-right" ng-if="!deal.divisor && deal.item.is_inventory">@{{ deal.qty % 1 == 0 ? Math.round(deal.qty) : deal.qty }} @{{ deal.item.unit}}</td>
                            <td class="col-md-2 text-right" ng-if="deal.divisor && deal.divisor != 1 && deal.item.is_inventory">@{{deal.dividend}} / @{{deal.divisor}}</td>
                            <td class="col-md-2 text-right" ng-if="deal.divisor && deal.divisor == 1 && deal.item.is_inventory">@{{deal.qty}}</td>
                            <td class="col-md-2 text-left" ng-if="!deal.item.is_inventory && deal.dividend==1">1 Unit</td>
                            <td class="col-md-2 text-left" ng-if="!deal.item.is_inventory && deal.dividend>1">@{{deal.dividend}} Unit</td> --}}
                            <td class="col-md-2 text-left">
                                <span ng-if="!deal.divisor && deal.item.is_inventory === 1">
                                    @{{ deal.qty % 1 == 0 ? Math.round(deal.qty) : deal.qty }} @{{ deal.item.unit}}
                                </span>
                                <span ng-if="deal.divisor !== 1 && deal.item.is_inventory === 1">
                                    @{{deal.dividend}} / @{{deal.divisor}}
                                </span>
                                <span ng-if="deal.divisor === 1 && deal.item.is_inventory === 1">
                                    @{{deal.qty}}
                                </span>
                                <span ng-if="deal.item.is_inventory === 0 && deal.dividend === 1">
                                    1 Unit
                                </span>
                                <span ng-if="deal.item.is_inventory === 0 && deal.dividend !== 1">
                                    @{{deal.dividend}} Unit
                                </span>
                            </td>
                            {{-- unit price --}}
                            <td class="col-md-1 text-right" ng-if="! deal.unit_price">@{{ (deal.amount / deal.qty) | currency: ""}}</td>
                            <td class="col-md-1 text-right" ng-if="deal.unit_price">@{{ deal.unit_price }}</td>
                            {{-- deal amount --}}
                            <td class="col-md-1 text-right" ng-if="deal.amount != 0">@{{ (deal.amount/100 * 100) | currency: "" }}</td>
                            <td class="col-md-1 text-right" ng-if="deal.amount == 0"><strong>FOC</strong></td>
                            <td class="col-md-1 text-center">
                                @unless(Auth::user()->hasRole('admin') or Auth::user()->hasRole('account') or Auth::user()->hasRole('supervisor'))
                                    @if($transaction->status === 'Draft' or $transaction->status === 'Pending' or $transaction->status === 'Confirmed')
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                    @else
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)" disabled>Delete</button>
                                    @endif
                                @else
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                @endunless
                            </td>
                        </tr>
                        @if($person->profile->gst)
                        <tr ng-if="deals.length">
                            <td></td>
                            <td colspan="3" class="col-md-2 text-center">
                                <strong>Subtotal</strong>
                            </td>
                            <td class="col-md-3 text-right">
                                <td class="text-right" ng-model="totalModel">@{{totalModel | currency: ""}}</td>
                            </td>
                        </tr>
                        <tr ng-if="deals.length">
                            <td></td>
                            <td colspan="3" class="col-md-2 text-center">
                                <strong>GST (7%)</strong>
                            </td>
                            <td class="col-md-3 text-right">
                                <td class="text-right" ng-model="totalModel">@{{(totalModel * 7/100) | currency: ""}}</td>
                            </td>
                        </tr>
                        @endif
                        <tr ng-if="delivery != 0">
                            <td></td>
                            <td colspan="3" class="col-md-2 text-center">
                                <strong>Delivery Fee</strong>
                            </td>
                            <td class="col-md-3 text-right">
                                <td class="text-right">@{{delivery | currency: ""}}</td>
                            </td>
                        </tr>
                        <tr ng-if="deals.length">
                            @if($person->profile->gst)
                            <td colspan="1" class="col-md-1 text-center"><strong>Total</strong></td>
                            <td colspan="3" class="text-right" ng-model="totalqtyModel"> <strong>@{{totalqtyModel.toFixed(4)}}</strong></td>
                            <td class="col-md-3 text-right">
                                {{-- <td class="text-right" ng-model="totalModel"><strong>@{{ (+(totalModel * 7/100) + totalModel) | currency: ""}}</strong></td> --}}
                                <td class="text-right"><strong>@{{ delivery ? (+(totalModel * 7/100) + totalModel + delivery/1).toFixed(2) : (+(totalModel * 7/100) + totalModel).toFixed(2)}}</strong></td>
                            </td>
                            @else
                            <td colspan="1" class="col-md-1 text-center"><strong>Total</strong></td>
                            <td colspan="3" class="text-right" ng-model="totalqtyModel"> <strong>@{{totalqtyModel.toFixed(4)}}</strong></td>
                            <td class="col-md-3 text-right">
                                <td class="text-right"><strong>@{{ delivery != 0 ? (+totalModel+delivery/1).toFixed(2) : totalModel.toFixed(2) }}</strong></td>
                            </td>
                            @endif
                        </tr>
                        <tr ng-show="(deals | filter:search).deals == 0 || ! deals.length">
                            <td colspan="7" class="text-center">No Records Found!</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-footer">
            <label ng-if="deals" class="pull-right totalnum" for="totalnum">Total of @{{deals.length}} entries</label>
        </div>
    </div>
</div>