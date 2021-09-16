<div ng-controller="itemController">

    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('product_id', 'ID', ['class'=>'control-label search-title']) !!}
            {!! Form::text('product_id', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.product_id',
                                                'placeholder'=>'ID',
                                                'ng-change'=>'searchDB()',
                                                'ng-model-options'=>'{ debounce: 500 }'
                                            ])
            !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('name', 'Product', ['class'=>'control-label search-title']) !!}
            {!! Form::text('name', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.name',
                                                'placeholder'=>'Product',
                                                'ng-change'=>'searchDB()',
                                                'ng-model-options'=>'{ debounce: 500 }'
                                            ])
            !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('remark', 'Desc', ['class'=>'control-label search-title']) !!}
            {!! Form::text('remark', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.remark',
                                                'placeholder'=>'Desc',
                                                'ng-change'=>'searchDB()',
                                                'ng-model-options'=>'{ debounce: 500 }'
                                            ])
            !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('is_active', 'Actve:', ['class'=>'control-label search-title']) !!}
            {!! Form::select('is_active',
                [
                    '1'=>'Yes',
                    '0'=>'No'
                ],
                null,
                ['class'=>'select form-control', 'ng-model'=>'search.is_active', 'ng-init'=>'search.is_active = "1"', 'ng-change'=>'searchDB()'])
            !!}
            </div>
        </div>
        {{-- <div class="form-group col-md-2 col-sm-4 col-xs-12">
            {!! Form::label('is_inventory', 'Is Inventory?', ['class'=>'control-label search-title']) !!}
            {!! Form::select('is_inventory',
                [
                    ''=>'All',
                    '1'=>'Yes',
                    '0'=>'No'
                ],
                null,
                ['class'=>'select form-control', 'ng-model'=>'search.is_inventory', 'ng-init'=>'search.is_inventory = "1"', 'ng-change'=>'searchDB()'])
            !!}
        </div> --}}
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('base_unit', 'Pc/ Ctn', ['class'=>'control-label search-title']) !!}
            {!! Form::text('base_unit', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.base_unit',
                                                'placeholder'=>'Pc/ Ctn',
                                                'ng-change'=>'searchDB()',
                                                'ng-model-options'=>'{ debounce: 500 }'
                                            ])
            !!}
            </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('itemcategories', 'Item Category', ['class'=>'control-label search-title']) !!}
            <select name="itemcategories" class="selectmultiple form-control" ng-model="search.itemcategories" ng-change="searchDB()" multiple>
                <option value="">All</option>
                @foreach($itemcategories->orderBy('name')->get() as $itemcategory)
                    <option value="{{$itemcategory->id}}">
                        {{$itemcategory->name}}
                    </option>
                @endforeach
            </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="form-group">
            {!! Form::label('item_group_id', 'Item Group', ['class'=>'control-label search-title']) !!}
            <select name="item_group_id" class="select form-control" ng-model="search.item_group_id" ng-change="searchDB()">
                <option value="">All</option>
                @foreach($itemGroups->orderBy('name')->get() as $itemGroup)
                    <option value="{{$itemGroup->id}}">
                        {{$itemGroup->name}}
                    </option>
                @endforeach
            </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-12">

            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
            @endif
        </div>

        <div class="col-md-5 col-sm-4 col-xs-12" style="padding-top:5px;">
            <div class="row">
                <div class="col-md-5 col-sm-5 col-xs-5">
                    Total Available
                </div>
                <div class="col-md-7 col-sm-7 col-xs-7 text-right" style="border: thin black solid">
                    <strong>@{{ totals.qty_now ? totals.qty_now : 0.00 | currency: "": 4}}</strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-sm-5 col-xs-5">
                    Total Booked
                </div>
                <div class="col-md-7 col-sm-7 col-xs-7 text-right" style="border: thin black solid">
                    <strong>@{{ totals.qty_order ? totals.qty_order : 0.00 | currency: "": 4}}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-4 col-xs-12 text-right">
            <div class="row" style="padding-right:18px;">
                <label>Display</label>
                <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                    <option ng-value="100">100</option>
                    <option ng-value="200">200</option>
                    <option ng-value="All">All</option>
                </select>
                <label>per Page</label>
            </div>
            <div class="row">
                <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
            </div>
        </div>
    </div>

    <div class="table-responsive" id="exportable_item" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('product_id')">
                    ID
                    <span ng-if="search.sortName == 'product_id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'product_id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortTable('name')">
                    Product
                    <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    Category
                </th>
                <th class="col-md-1 text-center">
                    Group
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('unit')">
                    Unit
                    <span ng-if="search.sortName == 'unit' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'unit' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('base_unit')">
                    Pc/ Ctn
                    <span ng-if="search.sortName == 'base_unit' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'base_unit' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('qty_now')">
                    Available Qty
                    <span ng-if="search.sortName == 'qty_now' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'qty_now' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('qty_order')">
                    Booked Qty
                    <span ng-if="search.sortName == 'qty_order' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'qty_order' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('lowest_limit')">
                    Threshold Limit
                    <span ng-if="search.sortName == 'lowest_limit' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'lowest_limit' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('publish')">
                    E-comm
                    <span ng-if="search.sortName == 'publish' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'publish' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('is_inventory')">
                    Is Inventory?
                    <span ng-if="search.sortName == 'is_inventory' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'is_inventory' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('is_active')">
                    Is Active?
                    <span ng-if="search.sortName == 'is_active' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'is_active' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    Action
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="item in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage" pagination-id="item">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1">
                        @{{ item.product_id }}
                    </td>
                    <td class="col-md-2">
                        @{{ item.name }}
                    </td>
                    <td class="col-md-1">
                        @{{ item.itemcategory ? item.itemcategory.name : null }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.item_group.name }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.unit }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.base_unit }}
                    </td>
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
                    <td class="col-md-1 text-right">
                        @{{ item.lowest_limit | currency: "": 4 }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.publish == 1 ? 'Yes':'No' }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.is_inventory == 1 ? 'Yes':'No' }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ item.is_active == 1 ? 'Yes':'No' }}
                    </td>
                    <td class="col-md-1 text-center">
                        @cannot('transaction_view')
                        <a href="/item/@{{ item.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                        @endcannot
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)" pagination-id="item"> </dir-pagination-controls>
    </div>

</div>