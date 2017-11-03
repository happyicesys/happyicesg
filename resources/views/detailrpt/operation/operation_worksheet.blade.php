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
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
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
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
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
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
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
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
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
                        'Red' => 'Red',
                    ],
                    null,
                    [
                        'class'=>'select form-control',
                        'ng-model'=>'search.color',
                        'ng-change'=>'searchDB()',
                        'ng-model-options'=>'{ debounce: 500 }'
                    ])
                !!}
            </div>
        </div>
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-8 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <button type="submit" class="btn btn-primary" form="export_excel" name="all" value="all"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export All Excel</button>
        <button type="submit" form="export_excel" class="btn btn-default" name="single" value="single"><i class="fa fa-file-excel-o"></i> Export Single Excel</button>
        <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12 text-right">
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
            <label class="" style="padding-right:18px;" for="totalnum">Showing @{{people.length}} of @{{totalCount}} entries</label>
        </div>
    </div>
</div>
{!! Form::close() !!}

    <div class="table-responsive" id="exportable" style="padding-top: 20px;">
        <table id="datatable" class="table table-list-search table-bordered">
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
                    Category
                    <span ng-if="search.sortName == 'custcategory' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'custcategory' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-2 text-center">
                    Note
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
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ person.custcategory }}
                    </td>
                    <td class="col-md-2">
                        {!! Form::textarea('operation_notes[@{{person.person_id}}]', null, ['class'=>'text-left form-control', 'rows'=>'2', 'style'=>'min-width: 150px; align-content: left;', 'ng-model'=>'person.operation_note', 'ng-change'=>'updateOpsNotes(person.person_id, person.operation_note)', 'ng-model-options'=>'{ debounce: 600 }']) !!}
                    </td>
                    <td class="col-md-1 text-center td_edit" style="min-width: 70px;" ng-repeat="alldata in alldata[$index]" ng-click="changeColor(alldata.id, $parent.$index, $index)" ng-style="{'background-color': alldata.color}">
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

<script src="/js/operation_worksheet.js"></script>