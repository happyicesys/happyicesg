
<div class="col-md-12">
    <div class="panel panel-success row">
        <div class="panel-heading">
            <div class="panel-title">         
                <div class="pull-left display_panel_title">
                    @unless($transaction->status == 'Cancelled')
                    <h3 class="panel-title"><strong>Selected : {{$person->cust_id}} - {{$person->company}}</strong></h3>
                    @else
                    <h3 class="panel-title"><strong><del>Selected : {{$person->cust_id}} - {{$person->company}}</del></strong></h3>
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

                            <td class="col-md-2 text-center" ng-if="deal.qty % 1 == 0">@{{ Math.round(deal.qty) }} @{{ deal.item.unit }}</td>
                            <td class="col-md-2 text-center" ng-if="deal.qty % 1 != 0">@{{ deal.qty }} @{{ deal.item.unit}}</td>
                            {{-- unit price --}}
                            <td class="col-md-1 text-right">@{{ (deal.amount / deal.qty).toFixed(2)}}</td>
                            {{-- deal amount --}}
                            <td class="col-md-1 text-right" ng-if="deal.amount != 0">@{{ (deal.amount/100 * 100).toFixed(2) }}</td>
                            <td class="col-md-1 text-right" ng-if="deal.amount == 0"><strong>FOC</strong></td>
                            <td class="col-md-1 text-center">
                                @if($transaction->pay_status == 'Owe')
                                    @if($transaction->status == 'Delivered')
                                        @can('transaction_deleteitem')
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                        @endcan
                                    @elseif($transaction->status == 'Cancelled')
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)" disabled>Delete</button>
                                    @else
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                    @endif
                                @else
                                    @cannot('transaction_view')
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                    @else
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)" disabled>Delete</button>
                                    @endcannot
                                @endif
                            </td>
                        </tr>
                        @if($person->profile->gst)
                        <tr ng-if="deals.length">
                            <td></td>
                            <td colspan="3" class="col-md-2 text-center">
                                <strong>Subtotal</strong>
                            </td>
                            <td class="col-md-3 text-right">
                                <td class="text-right" ng-model="totalModel">@{{totalModel}}</td>                            
                            </td>
                        </tr>
                        <tr ng-if="deals.length">
                            <td></td>
                            <td colspan="3" class="col-md-2 text-center">
                                <strong>GST (7%)</strong>
                            </td>
                            <td class="col-md-3 text-right">
                                <td class="text-right" ng-model="totalModel">@{{(totalModel * 7/100).toFixed(2)}}</td>                            
                            </td>
                        </tr>
                        @endif
                        <tr ng-if="deals.length">
                            @if($person->profile->gst)
                            <td class="col-md-1 text-center"><strong>Total</strong></td>
                            <td colspan="4" class="col-md-3 text-right">
                                {{-- <td class="text-right" ng-model="totalModel"><strong>@{{ ((totalModel * 7/100).toFixed(2) * 1 + totalModel * 1 )}}</strong></td> --}}
                                <td class="text-right" ng-model="totalModel"><strong>@{{ (totalModel * 107/100 ).toFixed(2)}}</strong></td>                                
                            </td>
                            @else
                            <td class="col-md-1 text-center"><strong>Total</strong></td>
                            <td colspan="4" class="col-md-3 text-right">
                                <td class="text-right" ng-model="totalModel"><strong>@{{ totalModel }}</strong></td>
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