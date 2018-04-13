@inject('people', 'App\Person')

@extends('template')
@section('title')
{{ $JOBCARD_TITLE }}
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/jobcard">
            <h1>{{ $JOBCARD_TITLE }} <i class="fa fa-th-list"></i> </h1>
        </a>
    </div>

        <div ng-app="app" ng-controller="jobController">
            <div class="panel panel-primary" >
                <div class="panel-body">
                    <div class="row" style="margin-top: -15px;">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <span class="pull-left">
                                    Job Card
                                </span>
                                <span class="pull-right">
                                    <button class="btn btn-success" data-toggle="modal" data-target="#job_modal" ng-click="createJobModal()">
                                        <i class="fa fa-plus"></i>
                                        Add Job
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('person_id',
                                [''=>'All'] + $people::whereHas('profile', function($q){
                                    $q->filterUserProfile();
                                })->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereIn('active', ['Yes', 'Pending'])->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                                null,
                                [
                                'id'=>'person_id',
                                'class'=>'select form-control',
                                'ng-model'=>'search.person_id',
                                'ng-change'=>'searchDB()'
                                ])
                            !!}                                                            
                        </div>                         
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('title', 'Affected Component', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('title', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.title',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'Affected Component',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ]) !!}
                        </div>  
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('created_from', 'Created From', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        name = "created_from"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "Created From"
                                        ng-model = "search.created_from"
                                        ng-change = "createdFromChange(search.created_from)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('created_from', search.created_from)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('created_from', search.created_from)"></span>
                            </div>
                        </div>                        
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('created_to', 'Created To', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        name = "created_to"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "Created To"
                                        ng-model = "search.created_to"
                                        ng-change = "createdToChange(search.created_to)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('created_to', search.created_to)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('created_to', search.created_to)"></span>
                            </div>
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

                    <div class="table-responsive" id="exportable_personmaintenance" style="padding-top:20px;">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>
                                <th class="col-md-2 text-center">
                                    <a href="" ng-click="sortTable('cust_id')">
                                    Customer
                                    <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>                                
                                <th class="col-md-2 text-center">
                                    <a href="" ng-click="sortTable('title')">
                                    Affected Component
                                    <span ng-if="search.sortName == 'title' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'title' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-3 text-center">
                                    <a href="" ng-click="sortTable('remarks')">
                                    Repair Details
                                    <span ng-if="search.sortName == 'remarks' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'remarks' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('is_refund')">
                                    Refund?
                                    <span ng-if="search.sortName == 'is_refund' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'is_refund' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>                                
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('created_by')">
                                    Created By
                                    <span ng-if="search.sortName == 'created_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'created_by' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>                                
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('created_at')">
                                    Created At
                                    <span ng-if="search.sortName == 'created_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'created_at' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>                                
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('updated_by')">
                                    Updated By
                                    <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('updated_at')">
                                    Updated At
                                    <span ng-if="search.sortName == 'updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1"></th>
                            </tr>
                            <tbody>
                                <tr dir-paginate="personmaintenance in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                    <td class="col-md-1 text-center">
                                        @{{ $index + indexFrom }}
                                    </td>
                                    <td class="col-md-2 text-center">
                                        <a href="/person/@{{personmaintenance.person.id}}/edit">
                                            (@{{personmaintenance.person.cust_id}}) @{{personmaintenance.person.company}}
                                        </a>
                                    </td>                                    
                                    <td class="col-md-2 text-center">
                                        @{{personmaintenance.title}}
                                    </td>
                                    <td class="col-md-3 text-left">
                                        @{{personmaintenance.remarks}}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{personmaintenance.is_refund == 1 ? 'Yes' : 'No'}}
                                    </td>                                      
                                    <td class="col-md-1 text-center">
                                        @{{personmaintenance.creator.name}}
                                    </td>                                      
                                    <td class="col-md-1 text-center">
                                        @{{personmaintenance.created_at}}
                                    </td>                                    
                                    <td class="col-md-1 text-center">
                                        @{{personmaintenance.updater.name}}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{personmaintenance.updated_at}}
                                    </td>                                    
                                    <td class="col-md-1 text-center">
                                        <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#personmaintenance_modal" ng-click="editPersonmaintenanceModal(personmaintenance)"><i class="fa fa-pencil-square-o"></i></button>
                                        <button class="btn btn-danger btn-sm" ng-click="removeEntry(personmaintenance.id)"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                                <tr ng-if="!alldata || alldata.length == 0">
                                    <td colspan="18" class="text-center">No Records Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="personmaintenance_modal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                            @{{form.id ? 'Edit Maintenance Log' : 'Add Maintenance Log'}}
                        </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Customer
                                    </label>
{{--                                     <select ui-select2 class="form-control" name="formsearches[]" ng-model="form.person_id" data-placeholder="Select..">
                                        @foreach($people::where('is_vending', 1)->orWhere('is_dvm', 1)->has('custcategory')->orderBy('cust_id', 'asc')->get() as $person)
                                            <option value="{{$person->id}}">
                                                {{$person->cust_id}} - {{$person->company}}
                                                @if($person->custcategory)
                                                    [{{$person->custcategory['name']}}]
                                                @endif
                                            </option>
                                        @endforeach
                                    </select> --}}
                                    <ui-select ng-model="form.person_id">
                                        <ui-select-match allow-clear="true">@{{$select.selected.cust_id}} - @{{$select.selected.company}}</ui-select-match>
                                        <ui-select-choices repeat="person.id as person in people | filter: $select.search">
                                            <div ng-bind-html="person.cust_id + ' - ' + person.company | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>                                                                        
                                </div>      
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Created On
                                    </label>
                                    {{-- <input type="text" name="title" class="form-control" ng-model="form.created_at"> --}}
                                    <datepicker>
                                        <input
                                            name = "created_at"
                                            type = "text"
                                            class = "form-control input-sm"
                                            placeholder = "Created At"
                                            ng-model = "form.created_at"
                                            ng-change = "createdAtChanged(form.created_at)"
                                        />
                                    </datepicker>                                    
                                </div>                                                          
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Affected Component
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="form.title">
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Repair Details
                                    </label>
                                    <textarea name="remarks" rows="5" class="form-control" ng-model="form.remarks"></textarea>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label>Need to refund? <input type="checkbox" ng-model="form.is_refund" ng-true-value="1" ng-false-value="0"></label>
                                </div>
                            </div>
                            <hr>
                            <div class="row" ng-if="form.is_refund">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Customer Name
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="form.refund_name">
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Customer Bank Name
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="form.refund_bank">
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Customer Bank Account#
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="form.refund_account">
                                </div>                                
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Customer Contact
                                    </label>
                                    <input type="text" name="title" class="form-control" ng-model="form.refund_contact">
                                </div>                                
                            </div>                            
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" ng-click="createPersonmaintenance()" data-dismiss="modal" ng-if="!form.id" ng-disabled="isFormValid()">Create</button>
                            <button type="button" class="btn btn-success" ng-click="editPersonmaintenance()" data-dismiss="modal" ng-if="form.id" ng-disabled="isFormValid()">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="/js/job.js"></script>
@stop        