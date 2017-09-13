@inject('custcategories', 'App\Custcategory')

<div class="row">
    {{-- {!! Form::open(['id'=>'search_form', 'method'=>'POST', 'action'=>['']])} --}}
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('cust_id', 'Customer ID', ['class'=>'control-label search-title']) !!}
                {!! Form::text('cust_id',
                    request('cust_id') ? request('cust_id') : null,
                    [
                        'class'=>'form-control input-sm',
                        'placeholder'=>'ID',
                    ])
                !!}
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('custcategory_id', 'Cust Category', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory_id', [''=>'All']+$custcategories::orderBy('name')->pluck('name', 'id')->all(),
                    request('custcategory_id') ? request('custcategory_id') : '2',
                    ['class'=>'select form-control'])
                !!}
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('company', 'Cust ID Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('company',
                    request('company') ? request('company') : null,
                    [
                        'class'=>'form-control input-sm',
                        'placeholder'=>'Cust ID Name',
                    ])
                !!}
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('product_id', 'Product ID:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('product_id',
                    request('product_id') ? request('product_id') : null,
                    [
                        'class'=>'form-control input-sm',
                        'placeholder'=>'Product ID',
                    ])
                !!}
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('name', 'Product Name:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('name',
                    request('name') ? request('name') : null,
                    [
                        'class'=>'form-control input-sm',
                        'placeholder'=>'Name',
                    ])
                !!}
            </div>

            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('is_inventory', 'Product Type', ['class'=>'control-label search-title']) !!}
                {!! Form::select('is_inventory',
                    ['1'=>'Inventory Item', 'All'=>'All'],
                    request('is_inventory') ? request('is_inventory') : '1',
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="btn-input-group">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> <span class="hidden-xs">Search</span></button>
                <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
            </div>
        </div>
    </div>

    <div class="table-responsive" id="exportable" style="padding-top:15px;">
        <table class="table table-fixed table-list-search table-hover table-bordered" style="font-size: 12px;">
            <thead>
            <tr style="background-color: #DDFDF8;">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    Cost Rate
                </th>
                <td class="col-md-1 text-left" ng-repeat="item in items">
                    (<strong>@{{item.product_id}}</strong>) @{{item.name}}
                </td>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="person in people">
                <td>(<strong>@{{person.cust_id}}</strong>) @{{person.company}}</td>
                <td class="col-md-1">
                    <input type="text" name="costrate[person.id]" class="text-right" ng-value="person.cost_rate" style="width: 55px;">
                </td>
                <td ng-repeat="item in items">
                    Retail Price
                    <input type="text" name="retailprice[@{{person.id}}-@{{item.id}}]" >
                    Quote Price
                    <input type="text" name="quoteprice[@{{person.id}}-@{{item.id}}]">
                </td>
            </tr>

            </tbody>
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

        </table>
    </div>
{{--
    <div class="col-md-12 col-sm-12 col-xs-12">
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" pagination-id="item"> </dir-pagination-controls>
        <label ng-if-"items" class="pull-right totalnum" for="totalnum">Showing @{{(items | filter:search).length}} of @{{items.length}} entries</label>
    </div> --}}