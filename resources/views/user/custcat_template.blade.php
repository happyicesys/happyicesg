@inject('custcategories', 'App\Custcategory')
@inject('custcategory_groups', 'App\CustcategoryGroup')

<div class="panel panel-info">
    <div class="panel-heading">
        <div class="panel-title pull-left">
            Customer Category
        </div>
        <div class="pull-right">
            <a href="custcat/create" class="btn btn-success btn-md">
                <i class="fa fa-plus"></i>
                Customer Category
            </a>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name', 'ng-change' => "searchDB()"]) !!}
            </div>
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('custcategory_group', 'Cust Category Group', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory_groups', [''=>'All'] + $custcategory_groups::orderBy('name')->pluck('name', 'id')->all(),
                    null,
                    [
                        'class'=>'selectmultiple form-control',
                        'ng-model'=>'search.custcategory_groups',
                        'multiple'=>'multiple',
                        'ng-change' => "searchDB()"
                    ])
                !!}
            </div>
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('active', 'Cust Status', ['class'=>'control-label search-title']) !!}
                <select name="active" id="active" class="selectmultiple form-control" ng-model="search.active" ng-change="searchDB($event)" multiple>
                    <option value="">All</option>
                    <option value="Potential">Potential</option>
                    <option value="New">New</option>
                    <option value="Yes">Active</option>
                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <option value="Pending">Pending</option>
                        <option value="No">Inactive</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                    <button class="btn btn-sm btn-primary" ng-click="exportData($event)">Export Excel</button>
                @endif
            </div>
            <div class="col-md-offset-8 col-md-4 col-xs-12 text-right">
                <div class="row">
                    <label for="display_num">Display</label>
                    <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
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

        <div class="row"></div>
        <div class="table-responsive" id="exportable_custcategory">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="#" ng-click="sortType = 'map_icon_file'; sortReverse = !sortReverse">
                        Map Icon
                        <span ng-show="sortType == 'map_icon_file' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'map_icon_file' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                        Name
                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-3 text-center">
                        <a href="#" ng-click="sortType = 'desc'; sortReverse = !sortReverse">
                        Description
                        <span ng-show="sortType == 'desc' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'desc' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="#" ng-click="sortType = 'desc'; sortReverse = !sortReverse">
                        Custcat Group
                        <span ng-show="sortType == 'desc' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'desc' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-1 text-center">
                        Cust Count
                    </th>
                    <th class="col-md-2 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage" pagination-id="exportable_custcategory" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-1 text-center">
                            <img src="@{{data.map_icon_file}}">
                        </td>
                        <td class="col-md-2 text-left">
                            @{{ data.name }}
                        </td>
                        <td class="col-md-3 text-left">
                            @{{ data.desc }}
                        </td>
                        <td class="col-md-2 text-left">
                            @{{ data.custcategory_group.name }}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{data.people_count}}
                        </td>
                        <td class="col-md-2 text-center">
                            <div class="btn-group">
                                <a href="/custcat/@{{ data.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-danger btn-sm btn-delete" ng-click="onCustcategoryDelete(data)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <dir-pagination-controls max-size="5" pagination-id="exportable_custcategory" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
    </div>
</div>

