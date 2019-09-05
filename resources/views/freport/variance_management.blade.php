@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('users', 'App\User')

<div ng-app="app" ng-controller="varianceManagementController">
    <div class="panel panel-primary">
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Variance Management
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                                <label class="control-label">Customer</label>
                                <select class="select form-control" ng-model="form.person_id">
                                    <option ng-value=""></option>
                                    @foreach($people::filterFranchiseePeople()->where('active', 'Yes')->get() as $person)
                                        <option ng-value="{{$person->id}}">
                                            {{$person->cust_id}} {{$person->company}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 form-group">
                                <label class="control-label">Date</label>
                                <input type="text" name="datein" class="date form-control" datetimepicker options='{format: "YYYY-MM-DD"}' ng-model="form.datein" placeholder="Default: Today">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Pieces</label>
                                <input type="text" name="pieces" class="form-control" ng-model="form.pieces" placeholder="Numbers Only">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Reason</label>
                                <textarea class="form-control" rows="2" ng-model="form.reason"></textarea>
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
            {!! Form::open(['id'=>'export_excel', 'method'=>'POST','action'=>['FtransactionController@indexApi']]) !!}
            <div class="row">
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('cust_id', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.cust_id',
                                                    'ng-change'=>'searchDB()',
                                                    'placeholder'=>'Cust ID',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('company', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.company',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'ID Name',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                    !!}
                </div>
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('datein_from', 'Date From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "datein_from"
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Date To"
                                ng-model = "search.datein_from"
                                ng-change = "dateInFromChanged(search.datein_from)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('datein_from', search.datein_from)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('datein_from', search.datein_from)"></span>
                    </div>
                </div>
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('datein_to', 'Date To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "datein_to"
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Date To"
                                ng-model = "search.datein_to"
                                ng-change = "dateInToChanged(search.datein_to)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('datein_to', search.datein_to)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('datein_to', search.datein_to)"></span>
                    </div>
                </div>
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    <div class="row col-md-12 col-sm-12 col-xs-12">
                        {!! Form::label('collection_shortcut', 'Date Shortcut', ['class'=>'control-label search-title']) !!}
                    </div>
                    <div class="btn-group">
                        <a href="" ng-click="onPrevDateClicked()" class="btn btn-default"><i class="fa fa-backward"></i></a>
                        <a href="" ng-click="onTodayDateClicked()" class="btn btn-default"><i class="fa fa-circle"></i></a>
                        <a href="" ng-click="onNextDateClicked()" class="btn btn-default"><i class="fa fa-forward"></i></a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    {{-- <button class="btn btn-primary" ng-click="exportData()">Export Excel</button> --}}
                    <button class="btn btn-primary" type="submit" form="export_excel" name="export_excel" value="export_excel"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"> Export Excel</span></button>
                </div>

                <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top:5px;">
                    <div class="row">
                        <div class="col-md-5 col-xs-5">
                            Total
                        </div>
                        <div class="col-md-7 col-xs-7 text-right" style="border: thin black solid">
                            <strong>@{{total_vend_amount ? total_vend_amount : 0.00 | currency: "": 2}}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6 col-xs-12 text-right">
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

                <div class="table-responsive" id="exportable" style="padding-top:20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        {{-- hidden table for excel export --}}
                        <tr class="hidden">
                            <td></td>
                            <td data-tableexport-display="always">Total Pieces</td>
                            <td data-tableexport-display="always" class="text-right">@{{total_pieces | currency: "": 2}}</td>
                        </tr>
                        <tr class="hidden" data-tableexport-display="always">
                            <td></td>
                        </tr>
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-3 text-center">
                                <a href="" ng-click="sortTable('cust_id')">
                                Customer
                                <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('datein')">
                                Date
                                <span ng-if="search.sortName == 'datein' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'datein' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('pieces')">
                                Pieces
                                <span ng-if="search.sortName == 'pieces' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'pieces' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('updated_by')">
                                Updated By
                                <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-4 text-center">
                                Reason
                            </th>
                            <th class="col-md-1"></th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="variance in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                <td class="col-md-1 text-center">
                                    @{{ $index + indexFrom }}
                                </td>
                                <td class="col-md-3 text-left">
                                    <a href="/person/@{{ variance.person_id }}">
                                        @{{variance.cust_id}} - @{{ variance.cust_id[0] == 'D' || variance.cust_id[0] == 'H' ? variance.name : variance.company }}
                                    </a>
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ variance.datein }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ variance.pieces }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ variance.updated_by }}
                                </td>
                                <td class="col-md-4 text-left">
                                    <textarea name="reasons[@{{variance.id}}]" class="form-control" style='min-width: 200px; align-content: left; font-size: 12px;' rows="2" ng-model="variance.reason" ng-change="changeReasons(variance.id, variance.remarks)" ng-model-options="{ debounce: 600 }"></textarea>
                                </td>
                                <td class="col-md-1 text-center">
                                    @if(!auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician'))
                                    <button class="btn btn-danger btn-sm" ng-click="removeEntry(ftransaction.id)"><i class="fa fa-times"></i></button>
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