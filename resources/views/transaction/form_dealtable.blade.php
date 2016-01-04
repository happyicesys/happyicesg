
<div class="col-md-12">
    <div class="panel panel-success row">
        <div class="panel-heading">
            <div class="panel-title">         
                <div class="pull-left display_panel_title">
                    <h3 class="panel-title"><strong>Selected : {{$person->cust_id}} - {{$person->company}}</strong></h3>
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
                        <tr dir-paginate="deal in deals | itemsPerPage:itemsPerPage"  current-page="currentPage">
                            <td class="col-md-1 text-center">@{{ $index + 1 }}</td>
                            <td class="col-md-1 text-center">@{{ deal.item.product_id }}</td>
                            <td class="col-md-5">@{{ deal.item.name }} @{{ deal.item.remark }}</td>

                            <td class="col-md-2 text-center" ng-if="deal.qty % 1 == 0">@{{ Math.round(deal.qty) }} @{{ deal.item.unit }}</td>
                            <td class="col-md-2 text-center" ng-if="deal.qty % 1 != 0">@{{ deal.qty }} @{{ deal.item.unit }}</td>
                            <td class="col-md-1 text-right">@{{ (deal.amount / deal.qty).toFixed(2)}}</td>
                            <td class="col-md-1 text-right">@{{ (deal.amount/100 * 100).toFixed(2) }}</td>
                            <td class="col-md-1 text-center">
                                @if($transaction->pay_status == 'Owe')
                                    @if($transaction->status == 'Delivered')
                                        @can('transaction_deleteitem')
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                        @endcan
                                    @else
                                        <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                                    @endif
                                @else
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)" disabled>Delete</button>
                                @endif
                            </td>
                        </tr>
                        <tr ng-if="deals.length">
                            <td class="col-md-1 text-center"><strong>Total</strong></td>
                            <td colspan="3" class="col-md-3 text-right">
                                <td class="text-right" ng-model="totalModel"><strong>@{{ totalModel }}</strong></td>                            
                            </td>
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