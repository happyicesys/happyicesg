@inject('people', 'App\Person')

<div class="col-md-12" ng-cloak>
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
                            <td class="col-md-1 text-center">@{{ deal.product_id }}</td>
                            <td class="col-md-5">@{{ deal.item_name }}<br> <small>@{{ deal.item_remark }}</small></td>
                            <td class="col-md-2 @{{deal.is_inventory===1 ? 'text-right' : 'text-left'}}">
                                <span ng-if="!deal.divisor && deal.is_inventory === 1">
                                    @{{ deal.qty % 1 == 0 ? Math.round(deal.qty) : deal.qty }} @{{ deal.unit }}
                                </span>
                                <span ng-if="(deal.divisor != 1.00 && deal.divisor != null)  && deal.is_inventory == 1">
                                    @{{deal.dividend | removeZero}} / @{{deal.divisor | removeZero}}
                                </span>
                                <span ng-if="deal.divisor == 1.00 && deal.is_inventory == 1">
                                    @{{deal.qty}}
                                </span>
                                <span ng-if="deal.is_inventory === 0 && deal.dividend == 1.00">
                                    1 Unit
                                </span>
                                <span ng-if="deal.is_inventory === 0 && deal.dividend != 1.00">
                                    @{{deal.dividend | removeZero}} Unit
                                </span>
                            </td>
                            {{-- unit price --}}
                            <td class="col-md-1 text-right" ng-if="! deal.unit_price">@{{ (deal.amount / deal.qty) | currency: ""}}</td>
                            <td class="col-md-1 text-right" ng-if="deal.unit_price">@{{ deal.unit_price }}</td>
                            {{-- deal amount --}}
                            <td class="col-md-1 text-right" ng-if="deal.amount != 0">@{{ (deal.amount/100 * 100) | currency: "" }}</td>
                            <td class="col-md-1 text-right" ng-if="deal.amount == 0"><strong>FOC</strong></td>
                            <td class="col-md-1 text-center">
                                @php
                                    $valid = false;
                                    $status = $transaction->status;

                                    if($transaction->is_freeze !== 1) {
                                        foreach(Auth::user()->roles as $role) {
                                            $access = $role->name;
                                            switch($access) {
                                                case 'admin':
                                                case 'account':
                                                case 'supervisor':
                                                    $valid = true;
                                                    break;
                                                default:
                                                    switch($status) {
                                                        case 'Draft':
                                                        case 'Pending':
                                                        case 'Confirmed':
                                                            $valid = true;
                                                            break;
                                                        default:
                                                            $valid = false;
                                                    }
                                            }
                                        }
                                    }else {
                                        $valid = false;
                                    }
                                @endphp

                                @if($valid)
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.deal_id)">Delete</button>
                                @else
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.deal_id)" disabled>Delete</button>
                                @endif
                            </td>
                        </tr>

                        <tr ng-if="delivery">
                            <td colspan="3" class="text-right">
                                <strong>Delivery Fee</strong>
                            </td>
                            <td colspan="2"></td>
                            <td class="col-md-1 text-right">
                                <strong>@{{delivery | currency: ""}}</strong>
                            </td>
                        </tr>
                        @if($person->profile->gst and $person->profile->is_gst_inclusive)
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalqtyModel}}</strong>
                                </td>
                                <td colspan="1"></td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalModel | currency: ""}}</strong>
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>GST ({{number_format($person->profile->gst_rate)}}%)</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{taxModel | currency: ""}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Exclude GST</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{subtotalModel | currency: ""}}
                                </td>
                            </tr>
                        @elseif($person->profile->gst and !$person->profile->is_gst_inclusive)
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Subtotal</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{subtotalModel | currency: ""}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>GST ({{number_format($person->profile->gst_rate)}}%)</strong>
                                </td>
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{taxModel | currency: ""}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalqtyModel}}</strong>
                                </td>
                                <td colspan="1"></td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalModel | currency: ""}}</strong>
                                </td>
                            </tr>
                        @else
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalqtyModel}}</strong>
                                </td>
                                <td colspan="1"></td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalModel | currency: ""}}</strong>
                                </td>
                            </tr>
                        @endif
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