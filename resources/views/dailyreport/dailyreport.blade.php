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
                <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()">
                    <option value="">All</option>
                    @foreach($users::orderBy('name')->get() as $user)
                        @if($user->hasRole('driver') and count($user->profiles) > 0)
                            <option value="{{$user->name}}">
                                {{$user->name}}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
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

    <div class="col-md-6 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ subtotal ? subtotal : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Today
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ today_total ? today_total : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Yesterday
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ yesterday_total ? yesterday_total : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Last 2 days
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ last_two_day_total ? last_two_day_total : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 50px;">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                <strong>
                    Commission ($)
                </strong>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ commission ? commission : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
</div>