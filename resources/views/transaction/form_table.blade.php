
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                @unless($transaction->status == 'Cancelled')
                <h3 class="panel-title"><strong>Create List : {{$person->cust_id}} - {{$person->company}}</strong></h3>
                @else
                <h3 class="panel-title"><strong><del>Create List : {{$person->cust_id}} - {{$person->company}}</del></strong></h3>
                @endunless
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div>
            @php
                $uoms = \App\Uom::orderBy('sequence', 'desc')->get();
            @endphp
            <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered table-condensed">
                    <tr style="background-color: #DDFDF8;">
                        <th class="text-center">
                            Item
                        </th>
                        @if($transaction->person->priceTemplate()->exists())
                            @if($uoms)
                                @foreach($uoms as $uom)
                                <th class="text-center">
                                    {{$uom->name}}
                                </th>
                                @endforeach
                            @endif
                        @else
                            <th class="text-center">
                                Qty
                            </th>
                        @endif
                        @if(!$transaction->is_discard)
                            <th class="text-center">
                                Retail Price ({{$transaction->person->profile->currency ? $transaction->person->profile->currency->symbol: '$'}})
                            </th>
                            <th class="text-center">
                                Quote Price ({{$transaction->person->profile->currency ? $transaction->person->profile->currency->symbol: '$'}})
                            </th>
                            <th class="text-center">
                                Amount ({{$transaction->person->profile->currency ? $transaction->person->profile->currency->symbol: '$'}})
                            </th>
                        @endif
                    </tr>

                    @if($transaction->person->priceTemplate()->exists())
                        {{-- <div class="form-group">
                            <label for="price_template">
                                Binded Price Template: {{$transaction->person->priceTemplate->name}} @if($transaction->person->priceTemplate->remarks) {{$transaction->person->priceTemplate->remarks}} @endif
                            </label>
                        </div>
                        @unless(count($transaction->person->priceTemplate->priceTemplateItems)>0)
                            <td class="text-center" colspan="12">No Records Found</td>
                        @else
                        @foreach($transaction->person->priceTemplate->priceTemplateItems as $priceTemplateItem)
                            @php
                                $itemUomArr = [];
                                if($priceTemplateItem->priceTemplateItemUoms()->exists()) {
                                    foreach($uoms as $uomIndex => $uom) {
                                        $itemUomArr[$uomIndex] = [
                                            'id' => $uom->id,
                                            'name' => $uom->name,
                                            'key_name' => strtolower($uom->name),
                                            'is_active' => false,
                                        ];
                                        foreach($priceTemplateItem->priceTemplateItemUoms as $priceTemplateItemUom) {
                                            if($uom->id == $priceTemplateItemUom->itemUom->uom->id) {
                                                $itemUomArr[$uomIndex]['is_active'] = true;
                                            }
                                        }
                                    }
                                }else {
                                    foreach($uoms as $uomIndex => $uom) {
                                        $itemUomArr[$uomIndex] = [
                                            'id' => $uom->id,
                                            'name' => $uom->name,
                                            'key_name' => strtolower($uom->name),
                                            'is_active' => false,
                                        ];
                                    }
                                    $itemUomArr[0]['is_active'] = true;
                                }
                            @endphp
                            <tr class="txtMult">
                                <td class="col-md-5 col-xs-4 hidden-xs">
                                        <strong>{{$priceTemplateItem->item->product_id}}</strong></span>
                                        - {{$priceTemplateItem->item->name}}
                                        <small>{{$priceTemplateItem->item->remark}}</small>
                                </td>
                                <td class="col-md-5 col-xs-4 hidden-lg hidden-md hidden-sm">
                                        <strong>{{$priceTemplateItem->item->product_id}}</strong><br>
                                        {{$priceTemplateItem->item->name}}<br>
                                        <small>{{$priceTemplateItem->item->remark}}</small>
                                </td>
                                @if($itemUomArr)
                                    @foreach($itemUomArr as $itemUom)
                                    <th class="col-md-1 col-xs-1">
                                        @if($transaction->status == 'Pending' or $transaction->status == 'Confirmed')
                                            <input type="text" name="{{$itemUom['key_name']}}[{{$priceTemplateItem->item->id}}]" style="min-width: 70px; max-width: 100px;" class="qtyClass form-control" {{$disabledStr}} {{$itemUom['is_active'] ? '' : 'disabled'}}/>
                                        @else
                                            @can('transaction_view')
                                            <input type="text" name="{{$itemUom['key_name']}}[{{$priceTemplateItem->item->id}}]" style="min-width: 70px; max-width: 100px;" class="qtyClass form-control" readonly="readonly"  {{$itemUom['is_active'] ? '' : 'disabled'}}/>
                                            @else
                                            <input type="text" name="{{$itemUom['key_name']}}[{{$priceTemplateItem->item->id}}]" style="min-width: 70px; max-width: 100px;" class="qtyClass form-control" {{$disabledStr}}  {{$itemUom['is_active'] ? '' : 'disabled'}}/>
                                            @endcan
                                        @endif
                                    </th>
                                    @endforeach
                                @endif
                                @if(!$transaction->is_discard)
                                <td class="col-md-2 col-xs-2">
                                    <strong>
                                        <input type="text" name="retail[{{$priceTemplateItem->item->id}}]"
                                        value="{{$priceTemplateItem->retail_price}}"
                                        class="text-right retailClass form-control" readonly="readonly"/>
                                    </strong>
                                </td>
                                <td class="col-md-2 col-xs-2">
                                    <strong>
                                        <input type="text" name="quote[{{$priceTemplateItem->item->id}}]"
                                        value="{{$priceTemplateItem->quote_price}}"
                                        class="text-right form-control quoteClass" {{$priceTemplateItem->item->is_editable_price_template ? '' : 'disabled'}}/>
                                    </strong>
                                </td>
                                <td class="col-md-2 col-xs-2">
                                    <input type="text" name="amount[{{$priceTemplateItem->item->id}}]"
                                    class="text-right form-control amountClass" style="min-width: 100px;" readonly="readonly"/>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        @endunless --}}

                    @else
                        @unless(count($prices)>0)
                        <td class="text-center" colspan="7">No Records Found</td>
                        @else
                        @foreach($prices as $price)
                            @if($price->is_active)
                            <tr class="txtMult">
                                <td class="col-md-5 col-xs-4 hidden-xs">
                                        <strong>{{$price->product_id}}</strong></span>
                                        - {{$price->name}}
                                        <small>{{$price->remark}}</small>
                                </td>
                                <td class="col-md-5 col-xs-4 hidden-lg hidden-md hidden-sm">
                                        <strong>{{$price->product_id}}</strong><br>
                                        {{$price->name}}<br>
                                        <small>{{$price->remark}}</small>
                                </td>
                                <td class="col-md-1 col-xs-2">
                                    @if($transaction->status == 'Pending' or $transaction->status == 'Confirmed')
                                    <input type="text" name="qty[{{$price->item_id}}]" style="min-width: 70px;" class="qtyClass form-control" {{$disabledStr}}/>
                                    @else
                                        @can('transaction_view')
                                        <input type="text" name="qty[{{$price->item_id}}]" style="min-width: 70px;" class="qtyClass form-control" readonly="readonly" />
                                        @else
                                        <input type="text" name="qty[{{$price->item_id}}]" style="min-width: 70px;" class="qtyClass form-control" {{$disabledStr}}/>
                                        @endcan
                                    @endif
                                </td>
                                @if(!$transaction->is_discard)
                                <td class="col-md-2 col-xs-2">
                                    <strong>
                                    <input type="text" name="retail[{{$price->item_id}}]"
                                    value="{{$price->retail_price}}"
                                    class="text-right retailClass form-control" readonly="readonly"/>
                                    </strong>
                                </td>
                                <td class="col-md-2 col-xs-2">
                                    <strong>
                                    @if($transaction->status == 'Cancelled')
                                        <input type="text" name="quote[{{$price->item_id}}]"
                                        value="{{$price->quote_price}}"
                                        class="text-right form-control quoteClass" readonly="readonly"/>
                                    @else
                                        <input type="text" name="quote[{{$price->item_id}}]"
                                        value="{{$price->quote_price}}"
                                        class="text-right form-control quoteClass" {{$disabledStr}}/>
                                    @endif
                                    </strong>
                                </td>
                                <td class="col-md-2 col-xs-2">
                                    <input type="text" name="amount[{{$price->item_id}}]"
                                    class="text-right form-control amountClass" style="min-width: 100px;" readonly="readonly"/>
                                </td>
                                @endif
                            </tr>
                            @endif
                        @endforeach
                        @endunless
                    @endif
                    <tr>
                        <td class="col-md-1 col-xs-2 text-center"><strong>Total</strong></td>
                        <td colspan="3" class="col-md-3 text-right">
                            @if(!$transaction->is_discard)
                            <td class="text-right" id="grandTotal" >
                                <strong>
                                    <input type="text" name="total_create" class="text-right form-control grandTotal" readonly="readonly" />
                                </strong>
                            </td>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>