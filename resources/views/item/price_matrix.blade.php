@inject('custcategories', 'App\Custcategory')

<div ng-controller="priceMatrixController">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('cust_id', 'Customer ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('cust_id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.cust_id',
                                                        'placeholder'=>'ID',
                                                        'ng-change'=>'searchDB()',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('custcategory_id', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('custcategory_id', [''=>'All']+$custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
                        [
                        'class'=>'select form-control',
                        'ng-model'=>'search.custcategory_id',
                        'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('company', 'Cust ID Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.company',
                                                        'placeholder'=>'ID Name',
                                                        'ng-change'=>'searchDB()',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                    !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('product_id', 'Product ID:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('product_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.product_id', 'placeholder'=>'ID', 'ng-change'=>'searchDB()', 'ng-model-options' => '{ debounce: 500 }']) !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('name', 'Product Name:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Product', 'ng-change'=>'searchDB()', 'ng-model-options'=>'{ debounce: 500 }']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-12">
            <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
        </div>

        <div class="pull-right col-md-3 col-sm-4 col-xs-12">
            <div class="pull-right display_panel_title">
                <label for="display_num">Display</label>
                <select ng-model="itemsPerPage" ng-init="itemsPerPage='100'">
                  <option ng-value="100">100</option>
                  <option ng-value="200">200</option>
                  <option ng-value="All">All</option>
                </select>
                <label for="display_num2" style="padding-right: 20px">per Page</label>
            </div>
        </div>
    </div>

    <div class="table-responsive" id="exportable" style="padding-top:15px;">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-left" ng-repeat="item in items">
                    (@{{item.product_id}}) @{{item.name}}
                </th>
            </tr>

            <tbody>
{{--                 <tr dir-paginate="item in items | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController" pagination-id="item">
                    <td class="col-md-1 text-center">@{{ number }} </td>
                    <td class="col-md-1 text-center">@{{ item.product_id }}</td>
                    <td class="col-md-2">@{{ item.name }}</td>
                    <td class="col-md-1 text-center">@{{ item.unit }}</td>
                    <td class="col-md-1 text-right">
                        <span ng-if="item.is_inventory === 1">
                            <strong>@{{item.qty_now | currency: "": 4 }}</strong>
                        </span>
                        <span ng-if="item.is_inventory === 0">
                            N/A
                        </span>
                    </td>
                    <td class="col-md-1 text-right">
                        <span ng-if="item.is_inventory === 1">
                            <a href="/item/qtyorder/@{{item.id}}">@{{ item.qty_order ? item.qty_order : 0 | currency: "": 4 }}</a>
                        </span>
                        <span ng-if="item.is_inventory === 0">
                            N/A
                        </span>
                    </td>
                    <td class="col-md-1 text-right">@{{ item.lowest_limit | currency: "": 4 }}</td>
                    <td class="col-md-1 text-center">@{{ item.publish == 1 ? 'Yes':'No'  }}</td>
                    <td class="col-md-1 text-center">@{{ item.is_inventory == 1 ? 'Yes':'No'  }}</td>
                    <td class="col-md-1 text-center">@{{ item.is_active == 1 ? 'Yes':'No'  }}</td>
                    <td class="col-md-1 text-center">
                        @cannot('transaction_view')
                        <a href="/item/@{{ item.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                        @endcannot
                    </td>
                </tr>
                <tr ng-if="(items | filter:search).length == 0 || ! items.length">
                    <td class="text-center" colspan="10">No Records Found!</td>
                </tr> --}}

            </tbody>
        </table>
    </div>
{{--
    <div class="col-md-12 col-sm-12 col-xs-12">
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" pagination-id="item"> </dir-pagination-controls>
        <label ng-if-"items" class="pull-right totalnum" for="totalnum">Showing @{{(items | filter:search).length}} of @{{items.length}} entries</label>
    </div> --}}
</div>