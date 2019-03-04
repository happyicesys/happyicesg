@inject('people', 'App\Person')
<div ng-controller="personassetCategoryController">
    <div class="panel panel-primary" >
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="pull-left">
                            Asset Category
                        </span>
                        <span class="pull-right">
                            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('hd_user') or auth()->user()->hasRole('operation'))
                            <button class="btn btn-success" data-toggle="modal" data-target="#personasset_modal" ng-click="createPersonassetModal()">
                                <i class="fa fa-plus"></i>
                                Add Asset Category
                            </button>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id',
                        [''=>'All'] + $people::whereHas('profile', function($q){
                            $q->filterUserProfile();
                        })->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereIn('active', ['Yes', 'Pending'])->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                        null,
                        [
                        'id'=>'person_id',
                        'class'=>'select2 form-control',
                        'ng-model'=>'search.person_id',
                        'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('code', 'Asset Code', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('code', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.code',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Asset Code',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('name', 'Asset Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('name', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.name',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Asset Name',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('brand', 'Asset Brand', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('brand', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.brand',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Asset Brand',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                    <div class="row">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                            <option ng-value="100">100</option>
                            <option ng-value="200">200</option>
                            <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>
                    <div class="row">
                        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive" id="exportable_personasset" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('code')">
                            Code
                            <span ng-if="search.sortName == 'code' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'code' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('name')">
                            Name
                            <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('brand')">
                            Brand
                            <span ng-if="search.sortName == 'brand' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'brand' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('size1')">
                            Size 1
                            <span ng-if="search.sortName == 'size1' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'size1' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('size2')">
                            Size 2
                            <span ng-if="search.sortName == 'size2' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'size2' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('weight')">
                            Weight
                            <span ng-if="search.sortName == 'weight' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'weight' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('capacity')">
                            Capacity
                            <span ng-if="search.sortName == 'capacity' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'capacity' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('specs1')">
                            Specs1
                            <span ng-if="search.sortName == 'specs1' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'specs1' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('specs2')">
                            Specs2
                            <span ng-if="search.sortName == 'specs2' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'specs2' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('specs3')">
                            Specs3
                            <span ng-if="search.sortName == 'specs3' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'specs3' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('person_id')">
                            Customer
                            <span ng-if="search.sortName == 'person_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'person_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                            <td class="col-md-1 text-center">
                                @{{ $index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.code}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.name}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.brand}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.size1}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.size2}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.weight}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.capacity}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.specs1}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.specs2}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.specs3}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.company}}
                            </td>
                            <td class="col-md-1 text-center">
                                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('operation'))
                                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#personasset_modal" ng-click="editPersonassetModal(data)"><i class="fa fa-pencil-square-o"></i></button>
                                    <button class="btn btn-danger btn-sm" ng-click="removeEntry(data.id)"><i class="fa fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                        <tr ng-if="!alldata || alldata.length == 0">
                            <td colspan="18" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>
    </div>

    <div class="modal fade" id="personasset_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    @{{form.id ? 'Edit Customer Asset' : 'Add Customer Asset'}}
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Code
                            </label>
                            <label for="required" class="control-label" style="color:red;">*</label>
                            <input type="text" name="title" class="form-control" ng-model="form.code">
                        </div>
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Name
                            </label>
                            <label for="required" class="control-label" style="color:red;">*</label>
                            <input type="text" name="title" class="form-control" ng-model="form.name">
                        </div>
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Brand
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.brand">
                        </div>

                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Size 1
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.size1">
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Size 2
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.size2">
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Weight
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.weight">
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Capacity
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.capacity">
                        </div>
                        <div class="form-group col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Specs 1
                            </label>
                            <textarea name="specs1" class="form-control" rows="5" ng-model="form.specs1"></textarea>
                            {{-- <input type="text" name="title" class="form-control" ng-model="form.capacity"> --}}
                        </div>
                        <div class="form-group col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Specs 2
                            </label>
                            <textarea name="specs2" class="form-control" rows="5" ng-model="form.specs2"></textarea>
                        </div>
                        <div class="form-group col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Specs 3
                            </label>
                            <textarea name="specs3" class="form-control" rows="5" ng-model="form.specs3"></textarea>
                        </div>
                        @if(auth()->user()->hasRole('hd_user'))
                            <div class="form-group col-md-12 col-sm-12 col-xs-12 hidden">
                                <input type="text" ng-model="form.person_id" ng-init="form.person_id={{$people::where('cust_id', 'B301')->first()->id}}">
                            </div>
                        @else
                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">
                                    Customer
                                </label>
                                <label for="required" class="control-label" style="color:red;">*</label>
                                <ui-select ng-model="form.person_id" on-select="onSelected($item)">
                                    <ui-select-match allow-clear="true">@{{$select.selected.cust_id}} - @{{$select.selected.company}}</ui-select-match>
                                    <ui-select-choices repeat="person.id as person in people | filter: $select.search">
                                        <div ng-bind-html="person.cust_id + ' - ' + person.company | highlight: $select.search"></div>
                                    </ui-select-choices>
                                </ui-select>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="createPersonasset()" data-dismiss="modal" ng-if="!form.id" ng-disabled="isFormValid()">Create</button>
                    <button type="button" class="btn btn-success" ng-click="updatePersonasset()" data-dismiss="modal" ng-if="form.id" ng-disabled="isFormValid()">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>