<div ng-controller="bomCategoryController">
    <div class="panel panel-primary" ng-cloak>
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Create Category
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Category Name</label>
                                <input type="text" name="name" class="form-control" ng-model="form.name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Remarks</label>
                                <textarea name="remark" class="form-control" rows="3" ng-model="form.remark"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-success btn-block" ng-click="addEntry()" ng-disabled="isFormValid()"><i class="fa fa-plus"></i> New Entry</button>
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
                    {!! Form::label('name', 'Category Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('name', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.name',
                                                    'ng-change'=>'searchDB()',
                                                    'placeholder'=>'Category Name',
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

            <div class="table-responsive" id="exportable_bomcategory" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('category_id')">
                            Category ID
                            <span ng-if="search.sortName == 'category_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'category_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-4 text-center">
                            <a href="" ng-click="sortTable('name')">
                            Category Name
                            <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-4 text-center">
                            <a href="" ng-click="sortTable('remark')">
                            Remarks
                            <span ng-if="search.sortName == 'remark' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'remark' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('updated_by')">
                            Updated By
                            <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr dir-paginate="bomcategory in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                            <td class="col-md-1 text-center">
                                @{{ $index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center">
                                <a href="#" data-toggle="modal" data-target="#category_modal" ng-click="editCategoryModal(bomcategory)">
                                    CAT @{{bomcategory.category_id}}
                                </a>
                            </td>
                            <td class="col-md-4 text-left">
                                @{{bomcategory.name}}
                            </td>
                            <td class="col-md-4 text-left">
                                @{{bomcategory.remark}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bomcategory.updater }}
                            </td>
                            <td class="col-md-1 text-center">
                                <button class="btn btn-danger btn-sm" ng-click="removeEntry(bomcategory.id)"><i class="fa fa-times"></i></button>
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

    <div class="modal fade" id="category_modal" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">
                    Edit Category
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Category ID
                            </label>
                            <input type="text" name="category_id" class="form-control" ng-model="categoryform.category_id">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Name
                            </label>
                            <input type="text" name="name" class="form-control" ng-model="categoryform.name">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Qty
                            </label>
                            <input type="text" name="qty" class="form-control" ng-model="categoryform.qty">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Remark
                            </label>
                            <textarea name="remark" class="form-control" ng-model="categoryform.remark" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="editCategory()" data-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>