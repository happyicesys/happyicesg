<div ng-controller="custTagsController">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('tag_name', 'Tag Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('tag_name', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.tag_name',
                                                        'placeholder'=>'Tag Name',
                                                        'ng-change' => 'searchDB()'
                                                    ])
                    !!}
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
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
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
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
            </div>
        </div>
    </div>

    <div class="row" style="padding-left: 15px;">
        <div class="col-md-6 col-sm-12 col-xs-12" style="padding-top: 20px;">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="col-md-offset-8 col-md-4 col-xs-12 text-right">
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

    <div id="exportable_cust_tags" class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortTable('tag_name')">
                    Tag Name
                    <span ng-if="search.sortName == 'tag_name' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'tag_name' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-9 text-center">
                    Customer(s) in Use
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage" pagination-id="customer_tags" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-2 text-center">
                        @{{ data.tag_name }}
                    </td>
                    <td class="col-md-9 text-left">
                        @{{ data.profiles }}
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
            <dir-pagination-controls max-size="5" pagination-id="customer_tags" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>