@inject('users', 'App\User')

<div ng-app="app" ng-controller="overtimeController" ng-cloak>
    <div class="panel panel-primary" >
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="pull-left">
                            Overtime
                        </span>
                        <span class="pull-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#overtime_modal" ng-click="createOvertimeModal()">
                                <i class="fa fa-plus"></i>
                                Add Overtime
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('user_id', 'Worker', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('user_id', [''=>'All']+$users::orderBy('name')->pluck('name', 'id')->all(), null,
                        [
                        'class'=>'select form-control',
                        'ng-model'=>'search.user_id',
                        'ng-change'=>'searchDB()'
                        ])
                    !!}
                </div>                                        
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('overtime_from', 'From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "overtime_from"
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "From"
                                ng-model = "search.overtime_from"
                                ng-change = "overtimeFromChange(search.overtime_from)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('overtime_from', search.overtime_from)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('overtime_from', search.overtime_from)"></span>
                    </div>
                </div>                        
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('overtime_to', 'To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "overtime_to"
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "To"
                                ng-model = "search.overtime_to"
                                ng-change = "overtimeToChange(search.overtime_to)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('overtime_to', search.overtime_to)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('overtime_to', search.overtime_to)"></span>
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

            <div class="table-responsive" id="exportable_overtime" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('overtime_date')">
                            Date
                            <span ng-if="search.sortName == 'overtime_date' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'overtime_date' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>                                
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('user_id')">
                            Worker
                            <span ng-if="search.sortName == 'user_id' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'user_id' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('hours')">
                            Hours
                            <span ng-if="search.sortName == 'hours' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'hours' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>                        
                        <th class="col-md-3 text-center">
                            <a href="" ng-click="sortTable('remarks')">
                            Remarks
                            <span ng-if="search.sortName == 'remarks' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'remarks' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>      
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('created_by')">
                            Created By
                            <span ng-if="search.sortName == 'created_by' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'created_by' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>                                                                                        
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr dir-paginate="overtime in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                            <td class="col-md-1 text-center">
                                @{{ $index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{overtime.overtime_date}}
                            </td>                                    
                            <td class="col-md-2 text-left">
                                @{{overtime.user.name}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{overtime.hours}}
                            </td>                            
                            <td class="col-md-3 text-left">
                                @{{overtime.remarks}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{overtime.creator.name}}
                            </td>                                                                                                                                                                                  
                            <td class="col-md-1 text-center">
                                <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#job_modal" ng-click="editOvertimeModal(ovetime)"><i class="fa fa-pencil-square-o"></i></button>
                                <button class="btn btn-danger btn-sm" ng-click="removeEntry(overtime.id)"><i class="fa fa-times"></i></button>
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

    <div class="modal fade" id="overtime_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    @{{form.id ? 'Edit Overtime' : 'Add Overtime'}}
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