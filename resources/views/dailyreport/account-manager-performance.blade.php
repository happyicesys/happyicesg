@inject('profiles', 'App\Profile')
@inject('customers', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('users', 'App\User')
@inject('zones', 'App\Zone')

@extends('template')
@section('title')
Performance
@stop
@section('content')

<div ng-app="app">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <h1>Performance</h1>
    </div>
    <div class="panel panel-default" ng-cloak>
        <div class="panel-heading">
            <ul class="nav nav-pills nav-justified" role="tablist">
                <li class="active"><a href="#daily_report" role="tab" data-toggle="tab"> Account Manager</a></li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane active" id="daily_report">
                  <div ng-controller="accountManagerPerformanceController">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                                    {!! Form::select('profile_id', [''=>'All']+
                                        $profiles::filterUserProfile()
                                            ->pluck('name', 'id')
                                            ->all(),
                                        null,
                                        [
                                        'class'=>'select form-control',
                                        'ng-model'=>'search.profile_id',
                                        'ng-change' => 'searchDB()'
                                        ])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('current_month', 'Current Month', ['class'=>'control-label search-title']) !!}
                                    <select class="select form-control" name="current_month" ng-model="search.current_month" ng-change="searchDB()">
                                        <option value="">All</option>
                                        @foreach($monthOptions as $key => $value)
                                            <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                                    {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], null,
                                        [
                                        'class'=>'select form-control',
                                        'ng-model'=>'search.status',
                                        'ng-change' => 'searchDB()'
                                        ])
                                    !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                                    {!! Form::text('cust_id', null,
                                                                [
                                                                    'class'=>'form-control input-sm',
                                                                    'ng-model'=>'search.cust_id',
                                                                    'placeholder'=>'Cust ID',
                                                                    'ng-change' => 'searchDB()'
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
                                                                        'ng-change' => 'searchDB()'
                                                                    ])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
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
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                                    <label class="pull-right">
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
                                            'ng-change' => "searchDB()"
                                        ])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
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
                                              [''=>'All', 'unassigned'=>'-- Unassigned --', 'total'=>'-- Daily Total --']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
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
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('zones', 'Zone', ['class'=>'control-label search-title']) !!}
                                    {!! Form::select('zones',
                                        $zones::orderBy('priority')->lists('name', 'id')->all(),
                                        null,
                                        [
                                            'class'=>'selectmultiple form-control',
                                            'ng-model'=>'search.zones',
                                            'multiple' => 'multiple'
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
                                    @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                                        <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                                    @endif

                                    <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
{{--
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="col-md-4 col-md-offset-8 col-xs-12 text-right">
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
                    </div> --}}

                    <div id="exportable_account_manager" class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
                        <div class=" table-responsive col-md-4 col-sm-6 col-xs-12" style="padding: 0px 0px 0px 0px;" ng-repeat="data in alldata">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th colspan="4" class="text-center">
                                        @{{data.title}}
                                        {{-- @{{data.title}} --}}
                                    </th>
                                </tr>
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        <a href="" ng-click="sortTable('date')">
                                        Date
                                        <span ng-if="search.sortName == 'date' && !search.sortBy" class="fa fa-caret-down"></span>
                                        <span ng-if="search.sortName == 'date' && search.sortBy" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        {{-- <a href="" ng-click="sortTable('account_manager_name')"> --}}
                                        Acc Manager
                                        {{-- <span ng-if="search.sortName == 'account_manager_name' && !search.sortBy" class="fa fa-caret-down"></span> --}}
                                        {{-- <span ng-if="search.sortName == 'account_manager_name' && search.sortBy" class="fa fa-caret-up"></span> --}}
                                    </th>
                                    <th class="col-md-1 text-center">
                                        {{-- <a href="" ng-click="sortTable('visited_total')"> --}}
                                        Outlet Visit
                                        {{-- <span ng-if="search.sortName == 'visited_total' && !search.sortBy" class="fa fa-caret-down"></span> --}}
                                        {{-- <span ng-if="search.sortName == 'visited_total' && search.sortBy" class="fa fa-caret-up"></span> --}}
                                    </th>
                                    <th class="col-md-1 text-center">
                                        {{-- <a href="" ng-click="sortTable('sales_total')"> --}}
                                        Sales S$
                                        {{-- <span ng-if="search.sortName == 'sales_total' && !search.sortBy" class="fa fa-caret-down"></span> --}}
                                        {{-- <span ng-if="search.sortName == 'sales_total' && search.sortBy" class="fa fa-caret-up"></span> --}}
                                    </th>
                                </tr>

                                {{-- <tbody> --}}
                                    <tr>
                                        <th class="col-md-1 text-center">
                                            Total
                                        </th>
                                        <th>
                                        </th>
                                        <th class="col-md-1 text-center">
                                            @{{data.visitTotal | currency: "": 0}}
                                        </th>
                                        <th class="col-md-1 text-right" ng-repeat-end>
                                            @{{data.salesTotal | currency: "": 2}}
                                        </th>
                                    </tr>

                                    <tbody ng-repeat="date in data.dates" ng-style="{'background-color': ($index%2==0) ? '#F1F1F1' : ''}">
                                        <tr ng-repeat="manager in date">
                                            <td class="col-md-1 text-center" style="font-size: 11px;">
                                                @{{manager.date}} <br>
                                                @{{manager.day}}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                @{{manager.account_manager_name}}
                                            </td>
                                            <td class="col-md-1 text-center">
                                                @{{manager.visits}}
                                            </td>
                                            <td class="col-md-1 text-right">
                                                @{{manager.sales | currency: "": 2}}
                                            </td>
                                        </tr>
                                    </tbody>
                                {{-- </tbody> --}}
                            </table>
                        </div>

{{--
                        <div>
                            <dir-pagination-controls max-size="5" pagination-id="dailyreport" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                        </div> --}}
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/dailyreport.js"></script>
<script>
    $(function() {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
</script>
@stop