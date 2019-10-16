<div ng-controller="dailyReportController">
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
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('date_from', 'Date From', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "date_from"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date From"
                            ng-model = "search.date_from"
                            ng-change = "dateFromChanged(search.date_from)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date_from', search.date_from)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date_from', search.date_from)"></span>
                </div>
            </div>
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('date_to', 'Date To', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "date_to"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date To"
                            ng-model = "search.date_to"
                            ng-change = "dateToChanged(search.date_to)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date_to', search.date_to)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date_to', search.date_to)"></span>
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
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    <select name="custcategory[]" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                        <option value="">All</option>
                        @foreach($custcategories::orderBy('name')->get() as $custcategory)
                        <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
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
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('tag', 'Tag', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('tag', [''=>'All', 'driver'=>'Driver', 'technician'=>'Technician'], null,
                        [
                        'class'=>'select form-control',
                        'ng-model'=>'search.tag',
                        'ng-change' => 'searchDB()'
                        ])
                    !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('driver', 'Delivered By', ['class'=>'control-label search-title']) !!}
                <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()" {{(auth()->user()->hasRole('driver') || auth()->user()->hasRole('technician')) ? 'readonly' : ''}}>
                    <option value="">All</option>
                    @foreach($users::orderBy('name')->get() as $user)
                        @if($user->hasRole('driver') or $user->hasRole('technician') and count($user->profiles) > 0)

                            <option value="{{$user->name}}" {{(auth()->user()->hasRole('driver') || auth()->user()->hasRole('technician')) && $user->name == auth()->user()->name ? 'selected' : ''}}>
                                {{$user->name}}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
{{--
            @foreach($users::orderBy('name')->get() as $user)
            @php
                if($user->name == auth()->user()->name) {
                    dd(auth()->user()->hasRole('driver'), $user->name, auth()->user()->name);
                }
            @endphp
            @endforeach --}}
{{--
            @unless(Auth::user()->hasRole('driver'))
                <div class="col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('user', 'User', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('user', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.user', 'ng-change'=>'searchDB()', 'ng-model-options'=>'{ debounce: 350 }', 'placeholder'=>'User']) !!}
                </div>
            @else
                <div class="col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('driver', 'User', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('driver', Auth::user()->name, [
                        'class'=>'form-control',
                        'placeholder'=>'User',
                        'readonly'=>'readonly'
                        ]) !!}
                </div>
            @endunless --}}
        </div>
    </div>

    <div class="row" style="padding-left: 15px;">
        <div class="col-md-6 col-sm-12 col-xs-12" style="padding-top: 20px;">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    {{-- <button class="btn btn-info" ng-click="searchDB($event)"><i class="fa fa-search"></i><span class="hidden-xs"></span> Search</button> --}}
                    <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                    <strong>
                        SubTotal
                    </strong>
                    <span ng-if="driver == 'technician'">
                        (of all of 051)
                    </span>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                    <strong>@{{ subtotal ? subtotal : 0.00 | currency: "": 2}}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;" ng-if="driver == 'driver'">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                    <strong>
                        Commission ($)
                    </strong>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                    <strong ng-if="search.driver">
                        @{{ totalcommission ? totalcommission : 0.00 | currency: "": 2}}
                    </strong>
                    <span ng-if="!search.driver">
                        Only Available when driver is selected
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xs-12 text-right">
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
    </div>

    <div id="exportable_daily_report" class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('transactions.delivery_date')">
                    Delivery Date
                    <span ng-if="search.sortName == 'transactions.delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'transactions.delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('driver')">
                    Delivered By
                    <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('total')">
                    Amount
                    <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="dailyreport" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-left">
                        @{{ deal.delivery_date }}
                    </td>
                    <td class="col-md-1 text-left">
                        @{{ deal.driver }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ deal.total | currency: "": 2}}
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
            <dir-pagination-controls max-size="5" pagination-id="dailyreport" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>