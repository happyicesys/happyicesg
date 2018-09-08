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
            <label class="pull-right totalnum" for="totalnum">Showing @{{alldata.length}} entries</label>
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
            <tbody>
                <tr ng-repeat="unitcost in alldata">
                    <td class="col-md-1 text-center">
                    <input type="checkbox" name="checkboxes[@{{unitcost.id}}]" value="@{{unitcost.id}}=@{{unitcost.profile_id}}=@{{unitcost.item_id}}=@{{unitcost.unitcost}}">
                    </td>
                    <td class="col-md-1 text-center">
                        @{{unitcost.id}}
                    </td>
                    <td class="col-md-2 text-center">
                        @{{unitcost.product_id}}
                    </td>
                    <td class="col-md-3 text-left">
                        @{{unitcost.item_name}}
                    </td>
                    <td class="col-md-3 text-left">
                        @{{unitcost.profile_name}}
                    </td>
                    <td class="col-md-2 text-right">
                        <input type="text" class="text-right form-control" ng-model="unitcost.unitcost"/>
                    </td>
                </tr>
            </tbody>
            <tr ng-if="!alldata.length">
                <td class="text-center" colspan="14">No Records Found!</td>
            </tr>
        </table>
    </div>
    {!! Form::close() !!}
</div>