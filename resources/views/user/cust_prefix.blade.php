@inject('custPrefixes', 'App\CustPrefix')

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
                <a href="cust-prefixes/create" class="btn btn-success">
                    <i class="fa fa-plus"></i>

                </a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group col-md-3 col-sm-4 col-xs-6">
                {!! Form::label('code', 'Prefix', ['class'=>'control-label search-title']) !!}
                {!! Form::text('code', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.code', 'placeholder'=>'Prefix']) !!}
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                    <button class="btn btn-sm btn-primary" ng-click="exportCustPrefixExcel($event)">Export Excel</button>
                @endif
            </div>
        </div>

        <div class="row"></div>
        <div class="table-responsive" id="exportable_cust_prefix">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="#" ng-click="sortType = 'code'; sortReverse = !sortReverse">
                        Prefix
                        <span ng-show="sortType == 'code' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'code' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-5 text-center">
                        Desc
                    </th>
                    <th class="col-md-2 text-center">
                      Cust Count
                    </th>
                    <th class="col-md-2 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                     <tr dir-paginate="custPrefix in custPrefixes | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage13" pagination-id="cust-prefix" current-page="currentPage13" ng-controller="repeatController13">
                        <td class="col-md-1 text-center">@{{ number }} </td>
                        <td class="col-md-2">@{{ custPrefix.code }}</td>
                        <td class="col-md-5">@{{ custPrefix.desc }}</td>
                        <td class="col-md-2 text-right">@{{ custPrefix.people_count }}</td>

                        <td class="col-md-2 text-center">
                            <a href="/cust-prefixes/@{{ custPrefix.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete13(custPrefix.id)">Delete</button>
                        </td>
                    </tr>
                    <tr ng-show="(custPrefixes | filter:search).length == 0 || ! custPrefixes.length">
                        <td colspan="6" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-footer">
          <dir-pagination-controls pagination-id="truck" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
          <label class="pull-right totalnum" for="totalnum">Showing @{{(custPrefixes | filter:search).length}} of @{{custPrefixes.length}} entries</label>
    </div>
</div>