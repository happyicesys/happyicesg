<div class="panel panel-default" ng-cloak>
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Performance
              </button>
            </div>
        </div>
    </div>

    <div class="panel-body">
      <div class="row">
            <div class="form-group col-md-3 col-sm-4 col-xs-12">
                {!! Form::label('date', 'Date', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "date"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date"
                            ng-model = "search.date"
                            ng-change = "dateChange('date', search.date)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date', search.date)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date', search.date)"></span>
                </div>
            </div>
            <div class="form-group col-md-3 col-sm-4 col-xs-12">
                {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                @if(auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                    <select name="account_manager" class="select form-control" ng-model="search.account_manager" ng-change="searchDB()" ng-init="merchandiserInit('{{auth()->user()->id}}')" disabled>
                        <option value="">All</option>
                        @foreach($users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                        <option value="{{$user->id}}">
                            {{$user->name}}
                        </option>
                        @endforeach
                    </select>
                @else
                    {!! Form::select('account_manager',
                            [''=>'All']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                            null,
                            [
                                'class'=>'select form-control',
                                'ng-model'=>'search.account_manager',
                                'ng-change'=>'searchDB()'
                            ])
                    !!}
                @endif
            </div>
      </div>

            <div class="row" style="padding-top: 20px;">
                <div class="col-md-4 col-xs-12">
                    {{-- <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button> --}}
                </div>
                <div class="col-md-4 col-md-offset-4 col-xs-12 text-right">
                    <div class="row" style="padding-right:18px;">
                        <label>Display</label>
                        <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                            <option ng-value="100">100</option>
                            <option ng-value="200">200</option>
                            <option ng-value="All">All</option>
                        </select>
                        <label>per Page</label>
                    </div>
                    <div class="row">
                        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                    </div>
                </div>
            </div>
        <hr>

        <div class="table-responsive" id="exportable" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('date')">
                        Date
                        <span ng-if="search.sortName == 'date' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'date' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('account_manager_id')">
                        Acc Manager
                        <span ng-if="search.sortName == 'account_manager_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'account_manager_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('created_count')">
                        # Created
                        <span ng-if="search.sortName == 'created_count' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'created_count' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('updated_count')">
                        # Updated
                        <span ng-if="search.sortName == 'updated_count' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'updated_count' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{data.date}}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{data.account_manager.name}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{data.created_count}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{data.updated_count}}
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>