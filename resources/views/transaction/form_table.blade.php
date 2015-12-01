@inject('deals', 'App\Deal')

<div class="col-md-12">
    <div class="panel panel-primary row">
        <div class="panel-body">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        Item Code
                    </th>
                    <th class="col-md-5 text-center">
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

               {{--  <tbody>
                    
                @unless($deals)
                <tr>
                <td class="text-center" colspan="8">No Records Found</td>
                </tr>
                @else                
                    @foreach($deals::where('transaction_id', $transaction->id) as $deal)
                        <tr>
                            <td class="col-md-1">{{ $deal->item_id }}</td>
                            <td class="col-md-5">{{ $deal->items->name }} - {{ $deal->items->remark }}</td>
                            <td class="col-md-5">{{ $deal->qty }} {{ $deal->items->unit }}</td>
                            <td class="col-md-5">{{ $deal->items->prices->quote_price }}</td>
                            <td class="col-md-5">{{ $deal->amount }}</td>
                        </tr>
                    @endforeach
                @endunless
                
                </tbody>   --}}  

                <tbody>
                    <tr dir-paginate="deal in deals | itemsPerPage:itemsPerPage"  current-page="currentPage">
                        <td class="col-md-1 text-center">@{{ deal.item.product_id }}</td>
                        <td class="col-md-5">@{{ deal.item.name }} @{{ deal.item.remark }}</td>
                        <td class="col-md-2 text-center">@{{ deal.qty }} @{{ deal.item.unit }}</td>
                        <td class="col-md-1 text-right">@{{ (deal.amount / deal.qty).toFixed(2)}}</td>
                        <td class="col-md-1 text-right">@{{ (deal.amount/100 * 100).toFixed(2) }}</td>
                        <td class="col-md-1 text-center">
                            @if($transaction->status == 'Pending')
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)">Delete</button>
                            @else
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(deal.id)" disabled>Delete</button>
                            @endif
                        </td>
                    </tr>
                    <tr ng-if="deals.length">
                        <td class="col-md-1 text-center"><strong>Total</strong></td>
                        <td colspan="4" class="col-md-2 text-right"><strong>@{{ getTotal() }}</strong></td>
                    </tr>
                    <tr ng-show="(deals | filter:search).deals == 0 || ! deals.length">
                        <td colspan="7" class="text-center">No Records Found!</td>
                    </tr>                         

                </tbody>                
            </table>            
        </div>

        <div class="panel-footer">
            <label ng-if="deals" class="pull-right totalnum" for="totalnum">Total of @{{deals.length}} entries</label>             
        </div>
    </div>
</div>    