<div ng-controller="monthlyReportController">
  <div class="col-md-12 col-xs-12">
      <div class="row">
          <div class="col-md-3 col-xs-6">
              <div class="form-group">
                  {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                  {!! Form::select('profile_id', [''=>'All']+
                      $profiles::filterUserProfile()
                          ->pluck('name', 'id')
                          ->all(), null,
                      [
                      'class'=>'select form-control',
                      'ng-model'=>'search.profile_id',
                      'ng-change'=>'searchDB()'
                      ])
                  !!}
              </div>
          </div>
          <div class="col-md-3 col-xs-6">
              <div class="form-group">
                  {!! Form::label('current_year', 'Current Year', ['class'=>'control-label search-title']) !!}
                  <select class="select form-control" name="current_year" ng-model="search.current_year" ng-change="searchDB()">
                      @foreach($yearOptions as $key => $value)
                          <option value="{{$key}}">{{$value}}</option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div class="col-md-3 col-xs-6">
              <div class="form-group">
                  {!! Form::label('cust_id', 'Cust ID', ['class'=>'control-label search-title']) !!}
                  {!! Form::text('cust_id', null,
                                              [
                                                  'class'=>'form-control input-sm',
                                                  'ng-model'=>'search.cust_id',
                                                  'placeholder'=>'Cust ID',
                                                  'ng-change'=>'searchDB()',
                                                  'ng-model-options'=>'{ debounce: 500 }'
                                              ])
                  !!}
              </div>
          </div>
          <div class="col-md-3 col-xs-6">
            <div class="form-group">
                {!! Form::label('id_prefix', 'ID Group', ['class'=>'control-label search-title']) !!}
                <select class="select form-group" name="id_prefix" ng-model="search.id_prefix" ng-change="searchDB()">
                    <option value="">All</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                    <option value="H">H</option>
                    <option value="R">R</option>
                    <option value="S">S</option>
                    <option value="V">V</option>
                    <option value="W">W</option>
                </select>
            </div>
        </div>
      </div>
      <div class="row">
          <div class="col-md-3 col-xs-6">
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
          <div class="col-md-3 col-xs-6">
  {{--
              <div class="form-group">
                  {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                  <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                      <option value="">All</option>
                      @foreach($custcategories::orderBy('name')->get() as $custcategory)
                      <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                      @endforeach
                  </select>
              </div> --}}
              <div class="form-group">
                  {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                  <label class="pull-right">
                        <input type="checkbox" name="exACategory" ng-model="search.exACategory" ng-change="onExACategoryChanged()">
                        <span style="margin-top: 5px; margin-right: 5px;">
                            Ex A
                        </span>
                      <input type="checkbox" name="exclude_custcategory" ng-model="search.exclude_custcategory" ng-true-value="'1'" ng-false-value="'0'" ng-change="searchDB()">
                      <span style="margin-top: 5px;">
                          Exclude
                      </span>
                  </label>
                  {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(),
                      null,
                      [
                          'class'=>'selectmultiple form-control',
                          'ng-model'=>'search.custcategory',
                          'multiple'=>'multiple',
                          'ng-change' => 'searchDB()'
                      ])
                  !!}
              </div>
          </div>
            <div class="col-md-3 col-xs-6">
                <div class="form-group">
                    {!! Form::label('custcategory_group', 'CustCategory Group', ['class'=>'control-label search-title']) !!}
                    <label class="pull-right">
                        {{-- <input type="checkbox" name="p_category" ng-model="search.p_category" ng-change="onPCategoryChanged()">
                        <span style="margin-top: 5px; margin-right: 5px;">
                            P
                        </span>
                        <input type="checkbox" name="exclude_custcategory_group" ng-model="search.exclude_custcategory_group" ng-true-value="'1'" ng-false-value="'0'" ng-change="searchDB()">
                        <span style="margin-top: 5px;">
                            Exclude
                        </span>--}}
                    </label>
                    {!! Form::select('custcategory_group', [''=>'All'] + $custcategoryGroups::orderBy('name')->pluck('name', 'id')->all(),
                        null,
                        [
                            'class'=>'selectmultiple form-control',
                            'ng-model'=>'search.custcategory_group',
                            'multiple'=>'multiple',
                            'ng-change' => "searchDB()"
                        ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-xs-6">
                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('status', [''=>'All', 'Pending'=>'Pending', 'Confirmed'=>'Confirmed', 'Delivered'=>'Delivered', 'Cancelled'=>'Cancelled'], null,
                        [
                        'class'=>'select form-control',
                        'ng-model'=>'search.status',
                        'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
            </div>
      </div>
      <div class="row">
          <div class="col-md-3 col-xs-6">
              <div class="form-group">
                  {!! Form::label('is_commission', 'Include Commission', ['class'=>'control-label search-title']) !!}
                  {!! Form::select('is_commission', ['0'=>'No', ''=>'Yes, all', '1'=>'VM Commission', '2'=> 'Supermarket Fee'], null,
                      [
                          'class'=>'select form-control',
                          'ng-model'=>'search.is_commission',
                          'ng-change'=>'searchDB()'
                      ])
                  !!}
              </div>
          </div>
          <div class="col-md-3 col-xs-6">
              <div class="form-group">
                  {!! Form::label('franchisee_id', 'Franchisee', ['class'=>'control-label search-title']) !!}
                  {!! Form::select('franchisee_id', [''=>'All', '0' => 'Own']+$franchisees::filterUserFranchise()->select(DB::raw("CONCAT(user_code,' (',name,')') AS full, id"))->orderBy('user_code')->pluck('full', 'id')->all(), null, ['id'=>'franchisee_id',
                      'class'=>'select form-control',
                      'ng-model'=>'search.franchisee_id',
                      'ng-change' => 'searchDB()'
                      ])
                  !!}
              </div>
          </div>
          <div class="col-md-3 col-sm-6">
              <div class="form-group">
                  {!! Form::label('person_active', 'Customer Status', ['class'=>'control-label search-title']) !!}
                  <select name="person_active" id="person_active" class="selectmultiple form-control" ng-model="search.person_active" ng-change="searchDB()" multiple>
                      <option value="">All</option>
                      <option value="Yes">Active</option>
                      <option value="New">New</option>
                      @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                          <option value="No">Inactive</option>
                          <option value="Pending">Pending</option>
                      @endif
                  </select>
              </div>
          </div>
          <div class="col-md-3 col-sm-6">
              <div class="form-group">
                  {!! Form::label('is_gst_inclusive', 'GST', ['class'=>'control-label search-title']) !!}
                  {!! Form::select('is_gst_inclusive',
                  [
                      '' => 'All',
                      'true' => 'Already added GST',
                      'false' => 'To add GST'
                  ],
                  null,
                  [
                      'class'=>'select form-control',
                      'ng-model'=>'search.is_gst_inclusive',
                      'ng-change' => 'searchDB()'
                  ])
              !!}
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-3 col-sm-6">
              <div class="form-group">
                  {!! Form::label('gst_rate', 'GST Rate (%)', ['class'=>'control-label search-title']) !!}
                  {!! Form::text('gst_rate', null,
                                                  [
                                                      'class'=>'form-control input-sm',
                                                      'ng-model'=>'search.gst_rate',
                                                      'ng-change'=>'searchDB()',
                                                      'placeholder'=>'GST Rate',
                                                      'ng-model-options'=>'{ debounce: 500 }'
                                                  ]) !!}
              </div>
          </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    {!! Form::label('item_id', 'Product', ['class'=>'control-label']) !!}
                    {!! Form::select('item_id',
                            [''=>'All']+$items::where('is_active', 1)->where('is_inventory', 1)->select(DB::raw("CONCAT(product_id,' - ',name) AS full, id"))->lists('full', 'id')->all(),
                            null,
                            [
                                'class'=>'selectmultiple form-control',
                                'ng-model'=>'search.item_id',
                                'ng-change'=>'searchDB()',
                                'multiple'=>'multiple'
                            ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    {!! Form::label('zone_id', 'Zone', ['class'=>'control-label']) !!}
                    {!! Form::select('zone_id',
                            [''=>'All']+ $zones::orderBy('priority')->lists('name', 'id')->all(),
                            null,
                            [
                                'class'=>'select form-control',
                                'ng-model'=>'search.zone_id',
                                'ng-change'=>'searchDB()'
                            ])
                    !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
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
      </div>
  </div>

  <div class="row" style="padding-left: 15px; padding-top:20px;">
      <div class="col-md-8 col-xs-12">
            <span class="row" ng-if="search.edited">
                <small>You have edited the filter, search?</small>
            </span>
            <button class="btn btn-sm btn-success" ng-click="onSearchButtonClicked($event)">
                Search
                <i class="fa fa-search" ng-show="!spinner"></i>
                <i class="fa fa-spinner fa-1x fa-spin" ng-show="spinner"></i>
            </button>
            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
            @endif
          <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
      </div>
  </div>

      <div class="table-responsive" id="exportable_monthlyreport" style="padding-top: 20px;">
          <table class="table table-list-search table-hover table-bordered">
              <tr style="background-color: #DDFDF8">
                  <th class="col-md-1 text-center">
                      #
                  </th>
                  <th class="col-md-1 text-center">
                      {{-- <a href="" ng-click="sortTable('month')"> --}}
                      Month
{{--
                      <span ng-if="search.sortName == 'month' && !search.sortBy" class="fa fa-caret-down"></span>
                      <span ng-if="search.sortName == 'month' && search.sortBy" class="fa fa-caret-up"></span> --}}
                  </th>
                <th ng-repeat="transaction in alldata | limitTo:3" class="col-md-2 text-center" colspan="2">
                    @{{transaction.title}} <br>
                    @{{transaction.year}}
                </th>
              </tr>

              <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center" colspan="2">
                </th>
                <th class="col-md-1 text-center">
                    Amt (S$)
                </th>
                <th class="col-md-1 text-center">
                    YoY (%)
                </th>
                <th class="col-md-1 text-center">
                    Amt (S$)
                </th>
                <th class="col-md-1 text-center">
                    YoY (%)
                </th>
                <th class="col-md-1 text-center">
                    Amt (S$)
                </th>
                <th class="col-md-1 text-center">
                    YoY (%)
                </th>
              </tr>

              <tbody>
                  <tr ng-repeat="month in months">
                    <td class="col-md-1 text-center">
                        @{{month.id}}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{month.short_name}}
                    </td>
                    <td ng-repeat-start="transaction in alldata | limitTo:3" class="col-md-1 text-right">
                        @{{transaction.data[month.id].sales | currency: "": 2 }}
                    </td>
                    <td ng-repeat-end class="col-md-1 text-right">
                        @{{transaction.data[month.id].yoy | currency: "": 2 }}
                    </td>
                  </tr>

                  <tr ng-if="alldata || alldata.length > 0">
                      <th class="col-md-1 text-center" colspan="2">
                        Total
                      </th>
                      <th ng-repeat-start="transaction in alldata | limitTo:3" class="col-md-1 text-right">
                          @{{transaction.thisSalesTotalYear | currency: "": 2}}
                      </th>
                      <th ng-repeat-end class="col-md-1 text-right">
                        @{{transaction.thisYoyTotalYear | currency: "": 2}}
                      </th>
                  </tr>
                  <tr ng-if="!alldata || alldata.length == 0">
                      <td colspan="14" class="text-center">No Records Found</td>
                  </tr>
              </tbody>
          </table>
      </div>
  </div>