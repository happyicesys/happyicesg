@inject('people', 'App\Person')
@inject('users', 'App\User')
@inject('custcategories', 'App\Custcategory')
@inject('bomcategories', 'App\Bomcategory')
@inject('bomcomponents', 'App\Bomcomponent')

<div ng-controller="maintenanceController">
    <div class="panel panel-primary" ng-cloak>
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        New Maintenance Record
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Customer</label>
                                <label style="color: red;">*</label>
                                <select class="selectform form-control" ng-model="form.person_id">
                                    <option ng-value=""></option>
                                    @foreach($people::where('is_vending', 1)->orWhere('is_dvm', 1)->has('custcategory')->orderBy('cust_id', 'asc')->get() as $person)
                                        <option ng-value="{{$person->id}}">
                                            {{$person->cust_id}} - {{$person->company}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Date</label>
                                <input type="text" name="date" class="date form-control" datetimepicker options='{format: "YYYY-MM-DD"}' ng-model="form.date" placeholder="Default: Today">
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Time</label>
                                <input type="text" name="time" class="time form-control" datetimepicker options='{format: "hh:mm A"}' ng-model="form.time" placeholder="Default: Now">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Technician</label>
                                <label style="color: red;">*</label>
                                <select class="selectform form-control" ng-model="form.technician_id">
                                    <option ng-value=""></option>
                                    @foreach($users::whereHas('roles', function($q){
                                                        $q->whereName('driver');
                                                })->get() as $user)
                                        <option ng-value="{{$user->id}}">
                                            {{$user->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Urgency</label>
                                <select class="selectform form-control" ng-model="form.urgency">
                                    <option value=""></option>
                                    <option value="ASAP">ASAP</option>
                                    <option value="When is Available">When is Available</option>
                                    <option value="Quaterly Maintanance">Quaterly Maintanance</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Time Spend</label>
                                <select class="selectform form-control" ng-model="form.time_spend">
                                    <option value=""></option>
                                    <option value="< 15"> < 15 </option>
                                    <option value="< 30"> < 30 </option>
                                    <option value="< 45"> < 45 </option>
                                    <option value="> 60"> > 60 </option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Affected Component</label>
                                <label style="color: red;">*</label>
                                <select class="selectform form-control" ng-model="form.bomcomponent_id">
                                    <option ng-value=""></option>
                                    @foreach($bomcomponents::all() as $bomcomponent)
                                        <option ng-value="{{$bomcomponent->id}}">
                                            {{$bomcomponent->bomcategory->name}} -
                                            {{$bomcomponent->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Type of Issue</label>
                                <label style="color: red;">*</label>
                                <select class="selectform form-control" ng-model="form.issue_type">
                                    <option value=""></option>
                                    <option value="Troubleshoot"> Troubleshoot</option>
                                    <option value="Component Fails"> Component Fails</option>
                                    <option value="Upgrade"> Upgrade</option>
                                    <option value="Routine Maintenance"> Routine Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                <label class="control-label">Done Solution</label>
                                <label style="color: red;">*</label>
                                <select class="selectform form-control" ng-model="form.solution">
                                    <option value=""></option>
                                    <option value="Repair"> Repair</option>
                                    <option value="Replace"> Replace</option>
                                    <option value="Adjust Setting"> Adjust Setting</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Remarks</label>
                                <textarea class="form-control" ng-model="form.remark" rows="3">
                                </textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-success btn-block" ng-click="addEntry()" ng-disabled="isFormValid()"><i class="fa fa-plus"></i> New Entry</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    <select class="selectsearch form-control" ng-model="search.person_id" ng-change="search.person_id" ng-model-options="{debounce: 500}">
                        <option value="">All</option>
                        @foreach($people::where('is_vending', 1)->orWhere('is_dvm', 1)->has('custcategory')->orderBy('cust_id', 'asc')->get() as $person)
                            <option value="{{$person->id}}">
                                {{$person->cust_id}} - {{$person->company}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
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
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
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
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    <div class="row col-md-12 col-sm-12 col-xs-12">
                        {!! Form::label('date_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
                    </div>
                    <div class="btn-group">
                        <a href="" ng-click="onPrevDateClicked()" class="btn btn-default"><i class="fa fa-backward"></i></a>
                        <a href="" ng-click="onTodayDateClicked()" class="btn btn-default"><i class="fa fa-circle"></i></a>
                        <a href="" ng-click="onNextDateClicked()" class="btn btn-default"><i class="fa fa-forward"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('custcategory_id', 'Category', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('custcategory_id', [''=>'All']+$custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
                        [
                        'class'=>'selectsearch form-control',
                        'ng-model'=>'search.custcategory_id',
                        'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    <label class="control-label">Technician</label>
                    <select class="selectsearch form-control" ng-model="search.technician_id">
                        <option value="">All</option>
                        @foreach($users::whereHas('roles', function($q){
                                            $q->whereName('driver');
                                    })->get() as $user)
                            <option value="{{$user->id}}">
                                {{$user->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    <label class="control-label">Affected Component</label>
                    <select class="selectsearch form-control" ng-model="search.bomcomponent_id">
                        <option value="">All</option>
                        @foreach($bomcomponents::all() as $bomcomponent)
                            <option value="{{$bomcomponent->id}}">
                                {{$bomcomponent->bomcategory->name}} -
                                {{$bomcomponent->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    <label class="control-label">Type of Issue</label>
                    <select class="selectsearch form-control" ng-model="search.issue_type">
                        <option value="">All</option>
                        <option value="Troubleshoot"> Troubleshoot</option>
                        <option value="Component Fails"> Component Fails</option>
                        <option value="Upgrade"> Upgrade</option>
                        <option value="Routine Maintenance"> Routine Maintenance</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 text-right">
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

            <div class="table-responsive" id="exportable_maintenance" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('maintenance_id')">
                            ID #
                            <span ng-if="search.sortName == 'maintenance_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'maintenance_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('person_id')">
                            Customer
                            <span ng-if="search.sortName == 'person_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'person_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('custcategory_id')">
                            Cust Category
                            <span ng-if="search.sortName == 'custcategory_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'custcategory_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('datetime')">
                            Date
                            <span ng-if="search.sortName == 'datetime' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'datetime' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            Time Arrive
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('time_spend')">
                            Time Spend
                            <span ng-if="search.sortName == 'time_spend' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'time_spend' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('technician_id')">
                            Technician
                            <span ng-if="search.sortName == 'technician_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'technician_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('urgency')">
                            Urgency
                            <span ng-if="search.sortName == 'urgency' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'urgency' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('bomcategory_id')">
                            Aff Category
                            <span ng-if="search.sortName == 'bomcategory_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'bomcategory_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('bomcomponent_id')">
                            Aff Component
                            <span ng-if="search.sortName == 'bomcomponent_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'bomcomponent_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('issue_type')">
                            Type of Issue
                            <span ng-if="search.sortName == 'issue_type' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'issue_type' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('solution')">
                            Done Solution
                            <span ng-if="search.sortName == 'solution' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'solution' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('remark')">
                            Remarks
                            <span ng-if="search.sortName == 'remark' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'remark' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            Raised By
                        </th>
                        <th class="col-md-1 text-center">
                            Last Updated By
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr dir-paginate="bommaintenance in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" pagination-id="bommaintenance">
                            <td class="col-md-1 text-center">
                                @{{ $index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center">
                                M @{{bommaintenance.maintenance_id}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{bommaintenance.cust_id}} - @{{bommaintenance.company}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{bommaintenance.custcategory_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.date}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.time}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.time_spend}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.technician_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.urgency}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.bomcategory_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.bomcomponent_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.issue_type}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.solution}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{bommaintenance.remark}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.creator}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bommaintenance.updater}}
                            </td>
                            <td class="col-md-1 text-center">
                                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('operation'))
                                    <button class="btn btn-danger btn-sm" ng-click="removeEntry(bommaintenance.id)"><i class="fa fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                        <tr ng-if="!alldata || alldata.length == 0">
                            <td colspan="24" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <dir-pagination-controls pagination-id="bommaintenance" max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>
    </div>
</div>