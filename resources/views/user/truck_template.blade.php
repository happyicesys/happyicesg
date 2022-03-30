@inject('trucks', 'App\Truck')

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">

            <div class="pull-left display_num">
                <label for="display_num">Display</label>
                <select ng-model="itemsPerPage10" ng-init="itemsPerPage10='100'">
                  <option>100</option>
                  <option>200</option>
                </select>
                <label for="display_num10" style="padding-right: 20px">per Page</label>
            </div>

            <div class="pull-right">
                <a href="truck/create" class="btn btn-success">
                    <i class="fa fa-plus"></i>

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
                    <button class="btn btn-sm btn-primary" ng-click="exportTruckExcel($event)">Export Excel</button>
                @endif
            </div>
        </div>

        <div class="row"></div>
        <div class="table-responsive" id="exportable_truck">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
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
                        Desc
                        <span ng-show="sortType == 'desc' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'desc' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="#" ng-click="sortType = 'height'; sortReverse = !sortReverse">
                        Height
                        <span ng-show="sortType == 'height' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'height' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-2 text-center">
                        Driver
                    </th>
                    <th class="col-md-2 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                     <tr dir-paginate="truck in trucks | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage10" pagination-id="truck" current-page="currentPage10" ng-controller="repeatController10">
                        <td class="col-md-1 text-center">@{{ number }} </td>
                        <td class="col-md-2">@{{ truck.name }}</td>
                        <td class="col-md-3">@{{ truck.desc }}</td>
                        <td class="col-md-2">@{{ truck.height }}</td>
                        <td class="col-md-2">@{{ truck.driver.name }}</td>
                        <td class="col-md-2 text-center">
                            <a href="/truck/@{{ truck.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete10(truck.id)">Delete</button>
                        </td>
                    </tr>
                    <tr ng-show="(trucks | filter:search).length == 0 || ! trucks.length">
                        <td colspan="6" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-footer">
          <dir-pagination-controls pagination-id="truck" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
          <label class="pull-right totalnum" for="totalnum">Showing @{{(trucks | filter:search).length}} of @{{trucks.length}} entries</label>
    </div>
</div>