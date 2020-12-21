<div class="panel panel-default" ng-cloak>
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-right">
              <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#meeting-minutes-modal" ng-click="onAddMeetingMinutesButtonClicked()">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Sales Meeting Minutes
              </button>
            </div>
        </div>
    </div>

    <div class="panel-body">
      <div class="row">
          <div class="form-group col-md-3 col-sm-6 col-xs-12">
              {!! Form::label('details', 'Details', ['class'=>'control-label search-title']) !!}
              {!! Form::text('details', null,
                                              [
                                                  'class'=>'form-control input-sm',
                                                  'ng-model'=>'search.details',
                                                  'placeholder'=>'Details',
                                                  'ng-change'=>'searchDB()',
                                                  'ng-model-options'=>'{ debounce: 500 }'
                                              ])
              !!}
          </div>

            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('date', 'Date', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "date"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date"
                            ng-model = "search.date"
                            ng-change = "dateChange('date', search.date)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date', search.date)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date', search.date)"></span>
                </div>
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('created_at', 'Created At', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "created_at"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Created At"
                            ng-model = "search.created_at"
                            ng-change = "dateChange('created_at', search.created_at)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('created_at', search.created_at)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('created_at', search.created_at)"></span>
                </div>
            </div>
      </div>

            <div class="row" style="padding-top: 20px;">
                <div class="col-md-4 col-xs-12">
                    {{-- <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button> --}}
                </div>
                <div class="col-md-4 col-md-offset-4 col-xs-12 text-right">
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
        <hr>

        <div class="table-responsive" id="exportable" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('date')">
                        Date
                        <span ng-if="search.sortName == 'date' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'date' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-5 text-center">
                        Details
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('created_at')">
                        Created By
                        <span ng-if="search.sortName == 'created_at' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'created_at' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center"></th>
                </tr>

                <tbody>
                    <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{data.date}}
                        </td>
                        <td class="col-md-5 text-left">
                            @{{data.details}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.creator.name}} <br>
                            @{{data.created_at}}
                        </td>
                        <td class="col-md-1 text-center">
                            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#meeting-minutes-modal" ng-click="onSingleEntryEdit(data)">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </button>
{{--
                            <button class="btn btn-danger btn-sm" ng-click="onSingleEntryRemove(data.id)">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button> --}}
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>

<div id="meeting-minutes-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">
                @{{form.id ? 'Edit Meeting Minutes' : 'New Meeting Minutes'}}
              </h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="name">
                    Date
                </label>
                <datepicker>
                    <input
                        name = "date"
                        type = "text"
                        class = "form-control input-sm"
                        placeholder = "Date"
                        ng-model = "search.date"
                        ng-change = "dateChange('date', search.date)"
                    />
                </datepicker>
            </div>
            <div class="form-group">
                <label for="remarks">
                    Remarks
                </label>
                <textarea class="form-control" rows="10" ng-model="form.remarks"></textarea>
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="!form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.date">Submit</button>
              <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.date">Save</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </div>

  </div>
</div>