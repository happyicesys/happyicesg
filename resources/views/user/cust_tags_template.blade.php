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
                <a href="custcat/create" class="btn btn-success">
                    <i class="fa fa-plus"></i>
                    Customer Tags
                </a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
            </div>
        </div>

        <div class="row"></div>
        <div class="table-responsive">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-2">
                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                        Category
                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-7">
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
                     <tr dir-paginate="custcat in custcats | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage5" pagination-id="custcat" current-page="currentPage5" ng-controller="repeatController5">
                        <td class="col-md-1 text-center">@{{ number }} </td>
                        <td class="col-md-2">@{{ custcat.name }}</td>
                        <td class="col-md-7">@{{ custcat.desc }}</td>

                        <td class="col-md-2 text-center">
                            <a href="/custcat/@{{ custcat.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete5(custcat.id)">Delete</button>
                        </td>
                    </tr>
                    <tr ng-show="(custcats | filter:search).length == 0 || ! custcats.length">
                        <td colspan="6" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-footer">
          <dir-pagination-controls pagination-id="custcat" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
          <label class="pull-right totalnum" for="totalnum">Showing @{{(custcats | filter:search).length}} of @{{custcats.length}} entries</label>
    </div>
</div>