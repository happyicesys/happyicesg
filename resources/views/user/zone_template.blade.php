@inject('zones', 'App\Zone')

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">

            <div class="pull-left display_num">
                <label for="display_num">Display</label>
                <select ng-model="itemsPerPage11" ng-init="itemsPerPage11='100'">
                  <option>100</option>
                  <option>200</option>
                </select>
                <label for="display_num11" style="padding-right: 20px">per Page</label>
            </div>

            <div class="pull-right">
                <a href="zone/create" class="btn btn-success">
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
                    <button class="btn btn-sm btn-primary" ng-click="exportZoneExcel($event)">Export Excel</button>
                @endif
            </div>
        </div>

        <div class="row"></div>
        <div class="table-responsive" id="exportable_zone">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-9 text-center">
                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                        Name
                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-2 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                     <tr dir-paginate="zone in zones | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage11" pagination-id="zone" current-page="currentPage11" ng-controller="repeatController11">
                        <td class="col-md-1 text-center">@{{ number }} </td>
                        <td class="col-md-2">@{{ zone.name }}</td>
                        <td class="col-md-2 text-center">
                            <a href="/zone/@{{ zone.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete11(zone.id)">Delete</button>
                        </td>
                    </tr>
                    <tr ng-show="(zones | filter:search).length == 0 || ! zones.length">
                        <td colspan="6" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-footer">
          <dir-pagination-controls pagination-id="zone" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
          <label class="pull-right totalnum" for="totalnum">Showing @{{(zones | filter:search).length}} of @{{zones.length}} entries</label>
    </div>
</div>