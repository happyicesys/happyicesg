@inject('people', 'App\Person')

<div class="col-md-12" ng-cloak>
    <div class="panel panel-success row">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title" style="margin-bottom: 15px;">
                    @unless($transaction->status == 'Cancelled' or $transaction->status == 'Deleted')
                    <h3 class="panel-title"><strong>Selected : {{$person->cust_id}} - {{$person->company}} ({{$person->name}})</strong></h3>
                    @else
                    <h3 class="panel-title"><strong><del>Selected : {{$person->cust_id}} - {{$person->company}} ({{$person->name}})</del></strong></h3>
                    @endunless
                </div>
                @if($transaction->status == 'Confirmed')
                    <div class="pull-right">
                        <div class="hidden-xs btn-group">
                            <button class="btn btn-success btn-md" ng-click="onStockButtonClicked($event, true)" ng-disabled="isStockAction">
                                Stock In 补货
                            </button>
                            <button class="btn btn-warning btn-md" ng-click="onStockButtonClicked($event, true)" ng-disabled="isStockAction">
                                Stock Rtn 退货
                            </button>
                            <button class="btn btn-danger btn-md" ng-click="onStockButtonClicked($event, false, ['051b'])" ng-disabled="isStockAction">
                                Melted 溶货
                            </button>
                        </div>
                    </div>
                    <div class="visible-xs">
                        <button class="btn btn-success btn-block" ng-click="onStockButtonClicked($event, true)" ng-disabled="isStockAction">
                            Stock In 补货
                        </button>
                        <button class="btn btn-warning btn-block" ng-click="onStockButtonClicked($event, true)" ng-disabled="isStockAction">
                            Stock Rtn 退货
                        </button>
                        <button class="btn btn-danger btn-block" ng-click="onStockButtonClicked($event, false, ['051b'])" ng-disabled="isStockAction">
                            Melted 溶货
                        </button>
                    </div>
                @endif
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
                        <th class="col-md-1 text-center">
                            Pieces
                        </th>
                        <th class="col-md-1 text-center">
                            Quantity
                        </th>
                        @if(!$transaction->is_discard)
                            <th class="col-md-1 text-center">
                                Unit Price
                            </th>
                        @endif
                        <th class="col-md-1 text-center">
                            Amount
                        </th>

                        <th class="col-md-1 text-center">
                            Action
                        </th>
                        {{-- <th>qty before</th> --}}
                        {{-- <th>qty after</th> --}}
                    </tr>

                    <tbody>
                        <tr ng-repeat="deal in deals">
                            <td class="col-md-1 text-center">@{{ $index + 1 }}</td>
                            <td class="col-md-1 text-center">@{{ deal.product_id }}</td>
                            <td class="col-md-5">@{{ deal.item_name }}<br> <small>@{{ deal.item_remark }}</small></td>
                            <td class="col-md-1 text-right">@{{ deal.pieces }}</td>
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
                                    @{{deal.dividend ? deal.dividend : 1 | removeZero}} Unit
                                </span>
                            </td>
                            {{-- unit price --}}
                            @if(!$transaction->is_discard)
                                <td class="col-md-1 text-right" ng-if="!deal.unit_price">@{{ (deal.amount / deal.dividend * deal.divisor) | currency: ""}}</td>
                                <td class="col-md-1 text-right" ng-if="deal.unit_price">@{{ deal.unit_price | currency: "" }}</td>
                                {{-- deal amount --}}
                            @endif
                            <td class="col-md-1 text-right" ng-if="deal.amount != 0 ">@{{ (deal.amount/100 * 100) | currency: "" }}</td>
                            <td class="col-md-1 text-right" ng-if="deal.amount == 0 && !deal.is_discard"><strong>FOC</strong></td>
                            {{-- <td class="col-md-1 text-right" ng-if="deal.amount == 0 && deal.is_discard"></td> --}}
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
                                                case 'accountadmin':
                                                case 'operation':
                                                    $valid = true;
                                                    break;
                                                case 'franchisee':
                                                case 'subfranchisee':
                                                case 'watcher':
                                                case 'hd_user':
                                                case 'event':
                                                    $valid = false;
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
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete($event, deal.deal_id)" ng-disabled="isStockAction && !deal.is_stock_action && deal.is_inventory">Delete</button>
                                @else
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete($event, deal.deal_id)" disabled>Delete</button>
                                @endif
                            </td>
                        {{-- <td>@{{deal.qty_before}}</td> --}}
                        {{-- <td>@{{deal.qty_after}}</td> --}}
                        </tr>

                        <tr ng-if="delivery">
                            <td colspan="3" class="text-right">
                                <strong>Delivery Fee</strong>
                            </td>
                            <td colspan="3"></td>
                            <td class="col-md-1 text-right">
                                <strong>@{{delivery}}</strong>
                            </td>
                        </tr>
                        @if($transaction->gst and $transaction->is_gst_inclusive)
                        @php
                            // dd('here1', $totalqtyModel, $totalModel, $subtotalModel);
                        @endphp

                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>GST ({{number_format($transaction->gst_rate)}}%)</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                <td></td>
                                @endif
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{taxModel}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Exclude GST</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                <td></td>
                                @endif
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{subtotalModel}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-md-1 text-right">
                                    @{{getTotalPieces()}}
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalqtyModel}}</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                    <td colspan="1"></td>
                                @endif
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalModel}}</strong>
                                </td>
                            </tr>
                        @elseif($transaction->gst and !$transaction->is_gst_inclusive)
                        @php
                            // dd('here2', $totalqtyModel, $totalModel, $subtotalModel);
                        @endphp
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Subtotal</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                <td></td>
                                @endif
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{subtotalModel}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>GST ({{number_format($transaction->gst_rate)}}%)</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                <td></td>
                                @endif
                                <td colspan="2"></td>
                                <td class="col-md-1 text-right">
                                    @{{taxModel}}
                                </td>
                            </tr>
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-md-1 text-right">
                                    @{{getTotalPieces()}}
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalqtyModel}}</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                    <td colspan="1"></td>
                                @endif
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalModel}}</strong>
                                </td>
                            </tr>
                        @else
                        @php
                            // dd('here3');
                        @endphp
                            <tr ng-if="deals.length>0">
                                <td colspan="3" class="text-right">
                                    <strong>Total</strong>
                                </td>
                                <td class="col-md-1 text-right">
                                    @{{getTotalPieces()}}
                                </td>
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalqtyModel}}</strong>
                                </td>
                                @if(!$transaction->is_discard)
                                    <td colspan="1"></td>
                                @endif
                                <td class="col-md-1 text-right">
                                    <strong>@{{totalModel}}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr ng-show="(deals | filter:search).deals == 0 || ! deals.length">
                            <td colspan="12" class="text-center">No Records Found!</td>
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