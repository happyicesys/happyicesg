@inject('profiles', 'App\Profile')

<div ng-controller="unitcostController">
    <div class="row">
        {!! Form::open(['id'=>'export_excel', 'method'=>'POST','action'=>['ItemController@getUnitcostIndexApi']]) !!}
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('product_id', 'ID:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('product_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.product_id', 'ng-change'=>'searchDB()', 'ng-model-options'=>'{ debounce: 500 }', 'placeholder'=>'ID']) !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('name', 'Product:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'ng-change'=>'searchDB()', 'ng-model-options'=>'{ debounce: 500 }', 'placeholder'=>'Product']) !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('profile_id', [''=>'All']+$profiles::lists('name', 'id')->all(), null,
                        [
                        'class'=>'select form-control',
                        'ng-model'=>'search.profile_id',
                        'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
            <div class="pull-left">
                <button type="submit" form="submit_unitcost" class="btn btn-success"> Batch Update</button>
                <button type="submit" form="export_excel" name="exportExcel" value="exportExcel" class="btn btn-primary" >Export Excel</button>
            </div>
            <label class="pull-right totalnum" for="totalnum">Showing @{{items.length * profiles.length ? items.length * profiles.length : 0}} entries</label>
        </div>
    </div>

    <div class="row"></div>
    {!! Form::open(['id'=>'submit_unitcost', 'method'=>'POST','action'=>['ItemController@batchUpdateUnitcost']]) !!}
    <div class="table-responsive" id="exportable_unitcost">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    <input type="checkbox" id="checkAll" />
                </th>
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortTable('product_id')">
                    ID
                    <span ng-if="search.sortName == 'product_id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'product_id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-3 text-center">
                    Product
                </th>
                <th class="col-md-3 text-center">
                    Profile
                </th>
                <th class="col-md-2 text-center">
                    Unit Cost
                </th>
            </tr>
            <tbody ng-repeat="item in items">
                <tr ng-repeat="profile in profiles" ng-init="number = countInit()">
                    <td class="col-md-1 text-center">{!! Form::checkbox('checkboxes[@{{number+1}}]') !!}</td>
                    <td class="col-md-1 text-center">@{{number + 1}}</td>
                    <td class="col-md-2 text-center">@{{ item.product_id }} </td>
                    <td class="col-md-3 text-left">@{{ item.name }}</td>
                    <td class="col-md-3 text-left">@{{ profile.name }}</td>
                    <td class="col-md-2 text-right">
                        <input type="text" name="unit_costs[@{{number+1}}]" class="text-right form-control" ng-init="unitCostModel = getUnitcostInit(profile.id, item.id)" ng-model="unitCostModel"/>
                    </td>
                    <td class="hidden">
                        <input type="text" name="profile_ids[@{{number+1}}]" ng-model="profile.id">
                    </td>
                    <td class="hidden">
                        <input type="text" name="item_ids[@{{number+1}}]" ng-model="item.id">
                    </td>
                </tr>
            </tbody>
            <tr ng-if="!profiles.length && !items.length">
                <td class="text-center" colspan="14">No Records Found!</td>
            </tr>
        </table>
    </div>
    {!! Form::close() !!}
</div>