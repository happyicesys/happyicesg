<div class="panel panel-default" ng-cloak>
    <div class="panel-heading">
        <div class="panel-title">
            Acc Holder
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                <label class="pull-right">
                    <input type="checkbox" name="strictCustId" ng-model="search.strictCustId" ng-change="searchDB($event)">
                    <span style="margin-top: 5px; margin-right: 5px; font-size: 12px;">
                        Strict
                    </span>
                </label>
                {!! Form::text('cust_id', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.cust_id',
                                                    'placeholder'=>'ID',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                <label class="pull-right">
                    <input type="checkbox" name="excludeCustCat" ng-model="search.excludeCustCat" ng-change="searchDB()">
                    <span style="margin-top: 5px; margin-right: 5px; font-size: 12px;">
                        Exclude
                    </span>
                </label>
                <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                    <option value="">All</option>
                    @foreach($custcategories::orderBy('name')->get() as $custcategory)
                    <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
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
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
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

            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('active', 'Status', ['class'=>'control-label search-title']) !!}
                <select name="active" id="active" class="selectmultiple form-control" ng-model="search.active" ng-change="searchDB()" multiple>
                    <option value="">All</option>
                    <option value="Yes">Active</option>
                    <option value="New">New</option>
                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                        <option value="No">Inactive</option>
                        <option value="Pending">Pending</option>
                    @endif
                </select>
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('tags', 'Tags', ['class'=>'control-label search-title']) !!}
                <select name="tags" id="tags" class="selectmultiple form-control" ng-model="search.tags" ng-change="searchDB()" multiple>
                    <option value="">All</option>
                    @foreach($persontags::orderBy('name')->get() as $persontag)
                        <option value="{{$persontag->id}}">
                            {{$persontag->name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('hd_user'))
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                {!! Form::select('profile_id', [''=>'All']+$profiles::filterUserProfile()->pluck('name', 'id')->all(), null, ['id'=>'profile_id',
                    'class'=>'select form-control',
                    'ng-model'=>'search.profile_id',
                    'ng-change' => 'searchDB()'
                    ])
                !!}
            </div>
            @endif
        </div>

        <div class="table-responsive" id="exportable_creation" style="padding-top: 20px;">
            <table class="table table-list-search table-bordered" style="font-size: 14px;">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('year')">
                        Year
                        <span ng-if="search.sortName == 'year' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'year' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('month')">
                        Month
                        <span ng-if="search.sortName == 'month' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'month' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('account_manager_id')">
                        Acc Manager
                        <span ng-if="search.sortName == 'account_manager_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'account_manager_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('created_cound')">
                        # New Acc Holding
                        <span ng-if="search.sortName == 'created_cound' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'created_cound' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                </tr>
                {{-- @{{alldata}} --}}

                <tbody>

                    <tr ng-repeat-end ng-repeat="data in alldata" ng-style="{'background-color': (data.month%2==0) ? '#F1F1F1' : ''}">
                        <td class="col-md-1 text-center">
                            @{{data.year}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.month_name}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.account_manager_name}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{data.created_count}}
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>