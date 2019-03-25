<div ng-app="app" ng-controller="operationWorksheetController" ng-cloak>
{!! Form::open(['id'=>'export_excel', 'method'=>'POST', 'action'=>['OperationWorksheetController@exportOperationExcel']]) !!}
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
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
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('id_prefix', 'ID Group', ['class'=>'control-label search-title']) !!}
                {!! Form::select('id_prefix',
                    [
                        '' => 'All',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                        'F' => 'F',
                        'G' => 'G',
                        'S' => 'S',
                        'R' => 'R',
                        'H' => 'H',
                        'V' => 'V',
                        'W' => 'W',
                    ],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.id_prefix',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(),
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.custcategory',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                {!! Form::text('cust_id',
                    null,
                    [
                        'class'=>'form-control',
                        'ng-model'=>'search.cust_id',
                        'placeholder'=>'Cust ID',
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('company',
                    null,
                    [
                        'class'=>'form-control',
                        'ng-model'=>'search.company',
                        'placeholder'=>'ID Name',
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('del_postcode', 'Postcode', ['class'=>'control-label search-title']) !!}
                {!! Form::text('del_postcode',
                    null,
                    [
                        'class'=>'form-control',
                        'ng-model'=>'search.del_postcode',
                        'placeholder'=>'Postcode',
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
                    ])
                !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('chosen_date', 'Today Date', ['class'=>'control-label search-title']) !!}
                <datepicker selector="form-control">
                    <input
                        type = "text"
                        name="chosen_date"
                        class = "form-control input-sm"
                        placeholder = "Today Date"
                        ng-model = "search.chosen_date"
                        ng-change = "onChosenDateChanged(search.chosen_date)"
                    />
                </datepicker>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('preferred_days', 'Preferred Day(s)', ['class'=>'control-label search-title']) !!}
                <select name="preferred_days" class="select form-control" ng-model="search.preferred_days" ng-change="searchDB()">
                    <option value="">All</option>
                    <option value="1">Mon</option>
                    <option value="2">Tues</option>
                    <option value="3">Wed</option>
                    <option value="4">Thu</option>
                    <option value="5">Fri</option>
                    <option value="6">Sat</option>
                    <option value="7">Sun</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('area_groups', 'Zone', ['class'=>'control-label search-title']) !!}
                <select name="area_groups" class="select form-control" ng-model="search.area_groups" ng-change="searchDB()">
                    <option value="">All</option>
                    <option value="1">West</option>
                    <option value="2">East</option>
                    <option value="3">Others</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('previous', 'Previous', ['class'=>'control-label search-title']) !!}
                {!! Form::select('previous',
                    [
                        'Last 7 days' => 'Last 7 days',
                        '' => 'Nil',
                        'Last 14 days' => 'Last 14 days',
                    ],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.previous',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('future', 'Future', ['class'=>'control-label search-title']) !!}
                {!! Form::select('future',
                    [
                        '' => 'Nil',
                        '2 days' => '2 days',
                        '5 days' => '5 days',
                    ],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.future',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('color', 'Show Color', ['class'=>'control-label search-title']) !!}
                {!! Form::select('color',
                    [
                        '' => 'All',
                        'Yellow' => 'Yellow',
                        'Yellow & Green' => 'Yellow & Green',
                        'Red' => 'Red',
                    ],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.color',
                        'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-8 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <button type="submit" class="btn btn-default" form="export_excel" name="excel_all" value="excel_all"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export All Excel</button>
        <button type="submit" form="export_excel" class="btn btn-default" name="excel_single" value="excel_single"><i class="fa fa-file-excel-o"></i> Export Filtered Excel</button>

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()" ng-if="people.length > 0"><i class="fa fa-map-o"></i> Generate Map</button>
        <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12 text-right">
        <div class="row">
            <label for="display_num">Display</label>
            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
                <option ng-value="100">100</option>
                <option ng-value="200">200</option>
                <option ng-value="All">All</option>
            </select>
            <label for="display_num2" style="padding-right: 20px">per Page</label>
        </div>
        <div class="row">
            <label class="" style="padding-right:18px;" for="totalnum">Showing @{{people.length}} of @{{totalCount}} entries</label>
        </div>
    </div>
</div>
{!! Form::close() !!}

    <div class="table-responsive" id="exportable" style="padding-top: 20px;">
        <table id="datatable" class="table table-list-search table-bordered table-fixedheader">
            <thead style="font-size: 12px;">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('del_postcode')">
                    Postcode
                    <span ng-if="search.sortName == 'del_postcode' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'del_postcode' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('cust_id')">
                    Cust ID
                    <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('company')">
                    ID Name
                    <span ng-if="search.sortName == 'company' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'company' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('custcategory')">
                    Cat
                    <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-2 text-center">
                    Ops Note
                </th>
                <th class="col-md-4 text-center">
                    Preferred Day(s)
                </th>
                <th class="col-md-2 text-center">
                    Zone
                </th>
                <th class="col-md-1 text-center" ng-repeat="date in dates" ng-class="todayDateChecker(date)">
                    @{{date | date : "yy-MM-dd"}}
                    <br>
                    @{{date | date : "EEE"}}
                </th>
            </tr>
            </thead>

            <tbody>
                <tr dir-paginate="person in people | itemsPerPage:itemsPerPage" pagination-id="operation_worksheet" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{$index + indexFrom}}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{person.del_postcode}}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{person.cust_id}}
                    </td>
                    <td class="col-md-1 text-left">
                        <a href="/person/@{{ person.person_id }}">
                            @{{ person.cust_id[0] == 'D' || person.cust_id[0] == 'H' ? person.name : person.company }}
                        </a>
                        <br>
                        <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(person)"><i class="fa fa-map-o"></i></button>
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ person.custcategory }}
                    </td>
                    <td class="col-md-2">
                        {!! Form::textarea('operation_notes[@{{person.person_id}}]', null, ['class'=>'text-left form-control', 'rows'=>'3', 'style'=>'min-width: 150px; align-content: left; font-size: 12px;', 'ng-model'=>'person.operation_note', 'ng-change'=>'updateOpsNotes(person.person_id, person.operation_note)', 'ng-model-options'=>'{ debounce: 600 }']) !!}
                    </td>
                    <td class="col-md-4" style="min-width: 120px;">
                        <div class="checkbox" style="margin-top: 0px;">
                            <span class="col-md-4 pull-left" style="padding-left: 0px;">
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.monday" ng-change="toggleCheckbox(person.monday, person.person_id, 'monday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Mon
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.tuesday" ng-change="toggleCheckbox(person.tuesday, person.person_id, 'tuesday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Tues
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.wednesday" ng-change="toggleCheckbox(person.wednesday, person.person_id, 'wednesday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Wed
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.thursday" ng-change="toggleCheckbox(person.thursday, person.person_id, 'thursday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Thu
                                </span>
                            </label>
                            </span>
                            <span class="col-md-4 pull-right" style="padding-left: 0px;">
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.friday" ng-change="toggleCheckbox(person.friday, person.person_id, 'friday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Fri
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.saturday" ng-change="toggleCheckbox(person.saturday, person.person_id, 'saturday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Sat
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.sunday" ng-change="toggleCheckbox(person.sunday, person.person_id, 'sunday')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Sun
                                </span>
                            </label>
                            </span>
                        </div>
                    </td>
                    <td class="col-md-2" style="min-width: 50px;">
                        <div class="checkbox" style="margin-top: 0px;">
                            <span class="col-md-4 pull-left" style="padding-left: 0px;">
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.west" ng-change="toggleZoneCheckbox(person.west, person.person_id, 'west')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    West
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.east" ng-change="toggleZoneCheckbox(person.east, person.person_id, 'east')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    East
                                </span>
                            </label>
                            <label>
                                <input name="checkbox[@{{person.person_id}}]" type="checkbox" ng-model="person.others" ng-change="toggleZoneCheckbox(person.others, person.person_id, 'others')" ng-true-value="'1'" ng-false-value="'0'">
                                <span style="margin-left: -5px; margin-top: 5px;">
                                    Others
                                </span>
                            </label>
                            </span>
                        </div>
                    </td>
                    <td class="col-md-1 text-center td_edit" style="min-width: 70px;" ng-repeat="alldata in alldata[$index]" ng-click="changeColor(alldata, $parent.$index, $index)" ng-style="{'background-color': getBackgroundColor(alldata, $parent.$index, $index)}">
                        &nbsp;@{{alldata.qty}}
                    </td>
                </tr>

                <tr ng-if="!people.length > 0">
                    <td colspan="18" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="operation_worksheet" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>

<div id="mapModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Plotted Map</h4>
      </div>
      <div class="modal-body">
        <div id="map"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script src="/js/operation_worksheet.js"></script>