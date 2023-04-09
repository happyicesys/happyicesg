<div class="panel panel-default" ng-cloak>
  <div class="panel-heading">
      <div class="panel-title">
      </div>
  </div>

  <div class="panel-body">

          <div class="row">
              <div class="form-group col-md-3 col-sm-4 col-xs-12">
                  {!! Form::label('date_from', 'Date From', ['class'=>'control-label search-title']) !!}
                  <div class="input-group">
                    <datepicker>
                        <input
                            name = "date_from"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date From"
                            ng-model = "search.date_from"
                            ng-change = "dateChange('date_from', search.date_from)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date_from', search.date_from)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date_from', search.date_from)"></span>
                </div>
              </div>
              <div class="form-group col-md-3 col-sm-4 col-xs-12">
                {!! Form::label('date_to', 'Date To', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                  <datepicker>
                      <input
                          name = "date_to"
                          type = "text"
                          class = "form-control input-sm"
                          placeholder = "Date To"
                          ng-model = "search.date_to"
                          ng-change = "dateChange('date_to', search.date_to)"
                      />
                  </datepicker>
                  <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date_to', search.date_to)"></span>
                  <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date_to', search.date_to)"></span>
                </div>
              </div>
              <div class="form-group col-md-3 col-sm-4 col-xs-12">
                {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                @if(auth()->user()->hasRole('merchandiser'))
                    <select name="account_manager" class="select form-control" ng-model="search.account_manager" ng-change="searchDB($event)" ng-init="merchandiserInit('{{auth()->user()->id}}')" disabled>
                        <option value="">All</option>
                        @foreach($users::whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                        <option value="{{$user->id}}">
                            {{$user->name}}
                        </option>
                        @endforeach
                    </select>
                @else
                    {!! Form::select('account_manager',
                            [''=>'All']+$users::whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                            null,
                            [
                                'class'=>'selectmultiple form-control',
                                'ng-model'=>'search.account_manager',
                                'ng-change'=>'searchDB($event)',
                                'ng-keydown' => 'searchDB($event)',
                                'multiple' => 'multiple'
                            ])
                    !!}
                @endif
              </div>
          </div>

          <div class="row" style="padding-top: 20px;">
              <div class="col-md-8 col-xs-12">
                  <span class="col-md-12 col-xs-12" ng-if="search.edited">
                      <small>You have edited the filter, search?</small>
                  </span>
                  <button class="btn btn-sm btn-success" ng-click="onSearchButtonClicked($event)">
                      Search
                      <i class="fa fa-search" ng-show="!spinner"></i>
                      <i class="fa fa-spinner fa-1x fa-spin" ng-show="spinner"></i>
                  </button>
                  @if(!auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                      @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                          <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                      @endif
                  @endif
                  <i class="fa fa-spinner fa-2x fa-spin" ng-show="spinner"></i>
              </div>
              <div class="col-md-4 col-xs-12 text-right">
                  <div class="row" style="padding-right:18px;">
                      <label>Display</label>
                      <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
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

      <div class="table-responsive" id="exportable" style="padding-top: 20px; overflow: scroll">
          <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
              <tr style="background-color: #DDFDF8">
                  <th class="col-md-1 text-center">
                      #
                  </th>
                  <th class="col-md-2 text-center">
                      Location Type
                  </th>
                  <th class="col-md-2 text-center">
                      Potential Customer
                  </th>
                  <th class="col-md-2 text-center">
                      Confirmed New Customer
                  </th>
              </tr>

              <tbody>
                  <tr dir-paginate="locationType in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                      <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                      </td>
                      <td class="col-md-2">
                        @{{ locationType.name }}
                      </td>
                      <td class="col-md-2 text-right">
                        @{{ locationType.potential_customers_count }}
                      </td>
                      <td class="col-md-2 text-right">
                        @{{ locationType.confirmed_customers_count }}
                      </td>
                  </tr>
                  <tr ng-if="alldata || alldata.length > 0">
                    <th colspan="2" class="text-center">
                      Total
                    </th>
                    <th class="col-md-2 text-right">
                      @{{ totals.potentialCustomers }}
                    </th>
                    <th class="col-md-2 text-right">
                      @{{ totals.confirmedCustomers }}
                    </th>
                  </tr>
                  <tr ng-if="!alldata || alldata.length == 0">
                      <td colspan="18" class="text-center">No Records Found</td>
                  </tr>
              </tbody>
          </table>
      </div>
  </div>
  <div class="panel-footer">
      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
  </div>
</div>