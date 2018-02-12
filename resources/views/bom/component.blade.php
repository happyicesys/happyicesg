@inject('bomcategories', 'App\Bomcategory')

<div ng-controller="bomComponentController">
    <div class="panel panel-primary" ng-cloak>
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Assign Component to Category
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Category Name</label>
                                <select class="select form-control" ng-model="form.category_id">
                                    <option ng-value=""></option>
                                    @foreach($bomcategories::all() as $bomcategory)
                                        <option ng-value="{{$bomcategory->id}}">
                                            CAT {{$bomcategory->category_id}} - {{$bomcategory->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <table class="table table-condensed table-bordered" style="margin-top: 15px;" ng-if="!isFormValid()">
                                    <tr style="background-color: #a3a3c2">
                                        <th class="col-md-1 text-center">
                                            #
                                        </th>
                                        <th class="col-md-4 text-center">
                                            Component Name
                                        </th>
                                        <th class="col-md-7 text-center">
                                            Remark
                                        </th>
                                    </tr>
                                    <tr ng-repeat="(index, formcomponent) in formcomponents">
                                        <td class="col-md-1 text-center">
                                            @{{index + 1}}
                                        </td>
                                        <td class="col-md-4">
                                            <input type="text" name="formcomponent_names[]" ng-model="formcomponent.name" class="form-control">
                                        </td>
                                        <td class="col-md-7">
                                            <textarea name="formcomponent_remarks[]" ng-model="formcomponent.remark" class="form-control" rows="1"></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn btn-primary btn-block" ng-click="addRow()" ng-disabled="isFormValid()"><i class="fa fa-plus"></i> Add Row</button>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn btn-success btn-block" ng-click="confirmComponents(form.category_id)" ng-disabled="isFormValid()"><i class="fa fa-check"></i> Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('category_id', 'Category ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('category_id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.category_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Category ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('category_name', 'Category Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('category_name', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.category_name',
                                                    'ng-change'=>'searchDB()',
                                                    'placeholder'=>'Category Name',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('component_id', 'Component ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('component_id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.component_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Component ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('component_name', 'Component Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('component_name', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.component_name',
                                                    'ng-change'=>'searchDB()',
                                                    'placeholder'=>'Component Name',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
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

            <div class="table-responsive" id="exportable_bomcomponent" style="padding-top:20px; font-size: 13px;">
                <table ng-repeat="bomcategory in alldata" class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th colspan="14" class="text-left">CAT@{{bomcategory.category_id}} - @{{bomcategory.name}}</th>
                    </tr>
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center" style="width:3%">
                            #
                        </th>
                        <th class="col-md-1 text-center" style="width:6%">
                            ID
                        </th>
                        <th class="col-md-2 text-center">
                            Name
                        </th>
                        <th class="col-md-2 text-center">
                            Remarks
                        </th>
                        <th class="col-md-1 text-center">
                            Qty
                        </th>
                        <th class="col-md-2 text-center">
                            Category Assignment
                        </th>
                        <th class="col-md-1 text-center">
                            Updated By
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr ng-repeat-start="(index, bomcomponent) in bomcategory.bomcomponents" style="background-color: #ddd1e7;">
                            <td class="col-md-1 text-left" style="width:3%">
                                @{{ index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center" style="width:6%">
                                COM @{{bomcomponent.component_id}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{bomcomponent.name}}
                            </td>
                            <td class="col-md-2 text-left" colspan="2">
                                @{{bomcomponent.remark}}
                            </td>
                            <td></td>
                            <td class="col-md-1 text-center">
                                @{{bomcomponent.updater.name}}
                            </td>
                            <td class="col-md-1 text-center">
                                <button class="btn btn-danger btn-xs" ng-click="removeEntry(bomcomponent.id)"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                        <tr ng-repeat="(index2, bompart) in bomcomponent.bomparts" ng-repeat-end style="background-color: #c1fcc1;">
                            <td class="col-md-1 text-left" style="width:3%">
                                @{{index + indexFrom}}.@{{index2 + 1}}
                            </td>
                            <td class="col-md-1 text-center" style="width:6%">
                                P @{{bompart.part_id}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{bompart.name}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{bompart.remark}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{bompart.qty}}
                            </td>
                            <td class="col-md-2 text-center">
                                <select ui-select2="select2Options" name="chosen[bompart.id]" ng-model="form.part_id" data-placeholder="Select.." multi>
                                    <option value=""></option>
{{--                                     <option ng-repeat="part in bomvending.choices" value="@{{part.id}}">
                                        @{{part.name}}
                                    </option> --}}
                                </select>
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bompart.updater.name}}
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
</div>