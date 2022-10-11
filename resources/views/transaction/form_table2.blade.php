<style>
    td {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                <h3 class="panel-title">
                    <strong>
                        <div ng-if="transaction.status == 'Cancelled'">
                            <del>
                                Create List : @{{transaction.person.cust_id}} - @{{transaction.person.company}}
                            </del>
                        </div>
                        <div ng-if="transaction.status != 'Cancelled'">
                            Create List : @{{transaction.person.cust_id}} - @{{transaction.person.company}}
                        </div>
                    </strong>
                </h3>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <label for="priceTemplate" ng-if="transaction.person.price_template">
            Binded Price Template: @{{transaction.person.price_template.name}}
            <span ng-if="transaction.person.price_template.remarks">
                &nbsp;@{{transaction.person.price_template.remarks}}
            </span>
        </label>
        <div class="hidden-xs">
            <div class="table-responsive" style="padding-top:10px;">
                <table class="table table-list-search table-hover table-bordered table-condensed">
                    <tr style="background-color: #DDFDF8;">
                        <th class="text-center">
                            Item
                        </th>
                        {{-- @{{transaction.person.price_template}} --}}
                        <th class="text-center" ng-if="transaction.person.price_template" ng-repeat="uom in uoms">
                            @{{uom.name}}
                        </th>
                        <th class="text-center" ng-if="!transaction.person.price_template">
                            Qty
                        </th>
                        <th class="text-center" ng-if="!transaction.is_discard">
                            Retail Price
                            <span ng-if="transaction.person.profile.currency">
                                (@{{transaction.person.profile.currency.symbol}})
                            </span>
                        </th>
                        <th class="text-center" ng-if="!transaction.is_discard">
                            Quote Price
                            <span ng-if="transaction.person.profile.currency">
                                (@{{transaction.person.profile.currency.symbol}})
                            </span>
                        </th>
                        <th class="text-center" ng-if="!transaction.is_discard">
                            Amount
                            <span ng-if="transaction.person.profile.currency">
                                (@{{transaction.person.profile.currency.symbol}})
                            </span>
                        </th>
                    </tr>

                    <tr ng-repeat="priceItem in priceItems">
                        <td class="col-md-3 col-xs-4" style="min-width: 150px;">
                            <div class="row">
                                <div class="col-md-3 col-xs-5 text-right">
                                    <strong>
                                        @{{priceItem.item.product_id}}
                                    </strong>
                                </div>
                                <div class="col-md-9 col-xs-7">
                                    @{{priceItem.item.name}}
                                    <small ng-if="priceItem.item.remark">
                                        <br>@{{priceItem.item.remark}}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="text-right col-md-1 col-xs-2" ng-if="transaction.person.price_template" ng-repeat="uom in uoms">
                            {{-- <input type="text" name="@{{uom.name}}[@{{priceItem.id}}]" ng-model="uom.name[priceItem.id]" class="form-control text-right" ng-disabled="!checkIsActiveUom(uom.id, priceItem)"/> --}}
                            <input type="number" name="@{{uom.name}}[@{{priceItem.id}}]" ng-model="priceItem.qty[uom.name]" ng-change="syncAmount(priceItem)" class="form-control text-right" ng-disabled="!checkIsActiveUom(uom.id, priceItem)"/>
                        </td>
                        <td class="text-right col-md-1 col-xs-2" ng-if="!transaction.person.price_template">
                            <input type="text" name="qty[@{{priceItem.id}}]"  ng-model="priceItem.qty['ctn']" ng-change="syncAmount(priceItem)" class="form-control text-right"/>
                        </td>
                        <td class="col-md-2 col-xs-2">
                            <strong>
                                <input type="text" name="retail[@{{priceItem.id}}]"
                                    value="@{{priceItem.retail_price}}"
                                    class="form-control text-right" readonly="readonly"/>
                            </strong>
                        </td>
                        <td class="col-md-2 col-xs-2">
                            <strong>
                                <input type="text" name="quote[@{{priceItem.id}}]"
                                    ng-model="priceItem.quote_price"
                                    ng-change="syncAmount(priceItem)"
                                    class="form-control text-right" ng-readonly="priceItem.item.is_inventory == 1 && transaction.person.price_template"/>
                            </strong>
                        </td>
                        <td class="col-md-2 col-xs-2">
                            <input type="text" name="amount[@{{priceItem.id}}]"
                                ng-model="priceItem.amount"
                                class="form-control text-right" readonly="readonly"/>
                        </td>
                    </tr>
                    <tr ng-if="priceItems.length > 0">
                        <th class="text-center" colspan="4" ng-if="!transaction.person.price_template">
                            Total
                        </th>
                        <th class="text-center" colspan="@{{uoms.length + 3}}" ng-if="transaction.person.price_template">
                            Total
                        </th>
                        <th class="text-right">
                            <input type="text" name="totalAmount"
                                ng-value="totalAmount"
                                class="form-control text-right" readonly="readonly"/>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="visible-xs">
            <ul class="list-group">
                <li class="list-group-item" ng-repeat="priceItem in priceItems">
                    <div class="row" style="font-size: 13px; padding-left: 3px;">
                        <strong>
                            @{{priceItem.item.product_id}} -  @{{priceItem.item.name}}
                        </strong>
                        <small ng-if="priceItem.item.remark">
                            <br>@{{priceItem.item.remark}}
                        </small>
                    </div>
                    <div class="row" style="padding-top:5px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr style="background-color: #DDFDF8;">
                                    <th class="text-center" ng-if="transaction.person.price_template" ng-repeat="uom in uoms">
                                        @{{uom.name}}
                                    </th>
                                    <th class="text-center" ng-if="!transaction.person.price_template">
                                        Qty
                                    </th>
                                </tr>
                                <tr>
                                    <td class="text-right col-md-1 col-xs-2" ng-if="transaction.person.price_template" ng-repeat="uom in uoms">
                                        <input type="number" name="@{{uom.name}}[@{{priceItem.id}}]" ng-model="priceItem.qty[uom.name]" ng-change="syncAmount(priceItem)" class="form-control text-right" ng-disabled="!checkIsActiveUom(uom.id, priceItem)"/>
                                    </td>
                                    <td class="text-right col-md-1 col-xs-2" ng-if="!transaction.person.price_template">
                                        <input type="text" name="qty[@{{priceItem.id}}]"  ng-model="priceItem.qty['ctn']" ng-change="syncAmount(priceItem)" class="form-control text-right"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td class="text-center" ng-if="!transaction.is_discard">
                                        <label>
                                            Retail
                                            <span ng-if="transaction.person.profile.currency">
                                                (@{{transaction.person.profile.currency.symbol}})
                                            </span>
                                        </label>
                                        <input type="text" name="retail[@{{priceItem.id}}]"
                                            value="@{{priceItem.retail_price}}"
                                            class="form-control text-right" readonly="readonly"/>
                                    </td>
                                    <td class="text-center" ng-if="!transaction.is_discard">
                                        <label>
                                            Quote
                                            <span ng-if="transaction.person.profile.currency">
                                                (@{{transaction.person.profile.currency.symbol}})
                                            </span>
                                        </label>
                                        <strong>
                                            <input type="text" name="quote[@{{priceItem.id}}]"
                                                ng-model="priceItem.quote_price"
                                                ng-change="syncAmount(priceItem)"
                                                class="form-control text-right" ng-readonly="priceItem.item.is_inventory == 1"/>
                                        </strong>
                                    </td>
                                    <td class="text-center" ng-if="!transaction.is_discard">
                                        <label>
                                            Amount
                                            <span ng-if="transaction.person.profile.currency">
                                                (@{{transaction.person.profile.currency.symbol}})
                                            </span>
                                        </label>
                                        <strong>
                                            <input type="text" name="amount[@{{priceItem.id}}]"
                                            ng-model="priceItem.amount"
                                            class="form-control text-right" readonly="readonly"/>
                                        </strong>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>