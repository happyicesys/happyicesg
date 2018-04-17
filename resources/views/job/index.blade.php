@inject('users', 'App\User')

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

        <div ng-app="app" ng-controller="jobController" ng-cloak>
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
                            {!! Form::label('task_name', 'Task Name', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('task_name', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.task_name',
                                                                'ng-change'=>'searchDB()',
                                                                'placeholder'=>'Task Name',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ]) !!}
                        </div>                          
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('from', 'From', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        name = "from"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "From"
                                        ng-model = "search.from"
                                        ng-change = "fromChange(search.from)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('from', search.from)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('from', search.from)"></span>
                            </div>
                        </div>                        
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('to', 'To', ['class'=>'control-label search-title']) !!}
                            <div class="input-group">
                                <datepicker>
                                    <input
                                        name = "to"
                                        type = "text"
                                        class = "form-control input-sm"
                                        placeholder = "To"
                                        ng-model = "search.to"
                                        ng-change = "createdToChange(search.to)"
                                    />
                                </datepicker>
                                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('to', search.to)"></span>
                                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('to', search.to)"></span>
                            </div>
                        </div>                         
                        <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('progress', 'Progress', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('progress',
                                [''=>'All', 'In Progress'=>'In Progress', 'Completed'=>'Completed'],
                                null,
                                [
                                'id'=>'progress',
                                'class'=>'select form-control',
                                'ng-model'=>'search.progress',
                                'ng-change'=>'searchDB()'
                                ])
                            !!}                                                            
                        </div>      
{{--                         <div class="form-group col-md-3 col-sm-6 col-xs-12">
                            {!! Form::label('workers', 'Worker', ['class'=>'control-label search-title']) !!}
                            <select name="workers" class="selectmultiple form-control" ng-model="search.workers" ng-change="searchDB()" multiple>
                                <option value="">All</option>
                                @foreach($users::where('is_active', 1)->orderBy('name')->get() as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>   --}}                                                                    

                                                                                                
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

                    <div class="table-responsive" id="exportable_job" style="padding-top:20px;">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('task_date')">
                                    Date
                                    <span ng-if="search.sortName == 'task_date' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'task_date' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>                                
                                <th class="col-md-2 text-center">
                                    <a href="" ng-click="sortTable('task_name')">
                                    Task
                                    <span ng-if="search.sortName == 'task_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'task_name' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-3 text-center">
                                    <a href="" ng-click="sortTable('remarks')">
                                    Remarks
                                    <span ng-if="search.sortName == 'remarks' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'remarks' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>      
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('created_by')">
                                    Issue By
                                    <span ng-if="search.sortName == 'created_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'created_by' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>                                                                                        
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('progress')">
                                    Progress (%)
                                    <span ng-if="search.sortName == 'progress' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'progress' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('is_verify')">
                                    Validation
                                    <span ng-if="search.sortName == 'is_verify' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'is_verify' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1"></th>
                            </tr>
                            <tbody>
                                <tr dir-paginate="job in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                    <td class="col-md-1 text-center">
                                        @{{ $index + indexFrom }}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{job.task_date}}
                                    </td>                                    
                                    <td class="col-md-2 text-left">
                                        @{{job.task_name}}
                                    </td>
                                    <td class="col-md-3 text-left">
                                        @{{job.remarks}}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{job.creator.name}}
                                    </td>                                                                          
                                    <td class="col-md-1 text-center">
                                        @{{job.progress}}
                                    </td>                                      
                                    <td class="col-md-1 text-left">
                                        <span class="col-md-12 col-sm-12 col-xs-12" ng-style="{color: (job.is_verify == null ? '' : (job.is_verify == 1 ? 'green' : 'red'))}">
                                            @{{job.is_verify == null ? 'Pending' : (job.is_verify == 1 ? 'Verified' : 'Rejected')}}
                                        </span>
                                        @if(auth()->user()->hasRole('admin'))
                                            <button ng-if="job.is_verify != '1' && job.progress == '100'" class="btn btn-sm btn-success" ng-click="verifyJob($event, job, 1)"><i class="fa fa-check"></i> Verify</button>
                                            <button ng-if="job.is_verify != '0' && job.progress == '100'" class="btn btn-sm btn-danger" ng-click="verifyJob($event, job, 0)"><i class="fa fa-cross"></i> Reject</button>
                                        @endif
                                    </td>                                                                     
                                    <td class="col-md-1 text-center">
                                        @if(auth()->user()->hasRole('admin'))
                                            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#job_modal" ng-click="editJobModal(job)"><i class="fa fa-pencil-square-o"></i></button>
                                            <button class="btn btn-danger btn-sm" ng-click="removeEntry(job.id)"><i class="fa fa-times"></i></button>
                                        @endif
                                        @if(!auth()->user()->hasRole('admin'))
                                            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#job_modal" ng-if="job.is_verify != '1'" ng-click="editJobModal(job)"><i class="fa fa-pencil-square-o"></i></button>
                                            <button class="btn btn-danger btn-sm" ng-if="job.is_verify != '1' && (job.creator.id == {{auth()->user()->id}})" ng-click="removeEntry(job.id)"><i class="fa fa-times"></i></button>                                        
                                        @endif
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

            <div class="modal fade" id="job_modal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                            @{{form.id ? 'Edit Job Card' : 'Add Job Card'}}
                        </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Task Name
                                    </label>
                                    <input type="text" name="task_name" class="form-control" ng-model="form.task_name">                                                                      
                                </div>      
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Date
                                    </label>
                                    <datepicker>
                                        <input
                                            name = "task_date"
                                            type = "text"
                                            class = "form-control input-sm"
                                            placeholder = "Date"
                                            ng-model = "form.task_date"
                                            ng-change = "taskDateChanged(form.task_date)"
                                        />
                                    </datepicker>                                    
                                </div>   
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Remarks
                                    </label>
                                    <textarea name="remarks" rows="5" class="form-control" ng-model="form.remarks"></textarea>
                                </div>    
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Progress (%)
                                    </label>
                                    <input type="text" name="progress" class="form-control" ng-model="form.progress" placeholder="Numbers only">
                                </div>                                                            
{{--                                 <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <label class="control-label">
                                        Workers
                                    </label>
                                    <ui-select ng-model="form.workers">
                                        <ui-select-match allow-clear="true">@{{$select.selected.cust_id}} - @{{$select.selected.company}}</ui-select-match>
                                        <ui-select-choices repeat="person.id as person in people | filter: $select.search">
                                            <div ng-bind-html="person.cust_id + ' - ' + person.company | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>                                                                        
                                </div>  --}}                                                                                         
                            </div>                           
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" ng-click="createJob()" data-dismiss="modal" ng-if="!form.id" ng-disabled="isFormValid()">Create</button>
                            <button type="button" class="btn btn-success" ng-click="editJob()" data-dismiss="modal" ng-if="form.id" ng-disabled="isFormValid()">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="/js/job.js"></script>
@stop        