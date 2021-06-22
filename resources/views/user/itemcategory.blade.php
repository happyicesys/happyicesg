@inject('itemcategories', 'App\Itemcategory')

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">

            <div class="pull-left display_num">
                <label for="display_num">Display</label>
                <select ng-model="itemsPerPage5" ng-init="itemsPerPage5='100'">
                  <option>100</option>
                  <option>200</option>
                </select>
                <label for="display_num2" style="padding-right: 20px">per Page</label>
            </div>

            <div class="pull-right">
                <a href="itemcategory/create" class="btn btn-success">
                    <i class="fa fa-plus"></i>
                    Item Category
                </a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group col-md-3 col-sm-4 col-xs-6">
                {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                    <button class="btn btn-sm btn-primary" ng-click="exportCustCatExcel($event)">Export Excel</button>
                @endif
            </div>
        </div>

        <div class="row"></div>
        <div class="table-responsive" id="exportable_custcategory">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-3 text-center">
                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                        Name
                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-6 text-center">
                        <a href="#" ng-click="sortType = 'desc'; sortReverse = !sortReverse">
                        Description
                        <span ng-show="sortType == 'desc' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'desc' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-2 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                     <tr dir-paginate="itemcategory in itemcategories | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage6" pagination-id="itemcategory" current-page="currentPage6" ng-controller="repeatController6">
                        <td class="col-md-1 text-center">@{{ number }} </td>
                        <td class="col-md-3">@{{ itemcategory.name }}</td>
                        <td class="col-md-6">@{{ itemcategory.desc }}</td>
                        <td class="col-md-2 text-center">
                            <a href="/itemcategory/@{{ itemcategory.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete6(itemcategory.id)">Delete</button>
                        </td>
                    </tr>
                    <tr ng-show="(itemcategories | filter:search).length == 0 || ! itemcategories.length">
                        <td colspan="6" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-footer">
          <dir-pagination-controls pagination-id="itemcategory" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
          <label class="pull-right totalnum" for="totalnum">Showing @{{(itemcategories | filter:search).length}} of @{{itemcategories.length}} entries</label>
    </div>
</div>