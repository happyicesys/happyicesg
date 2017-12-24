@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('users', 'App\User')
@extends('template')
@section('title')
{{ $FRANCHISE_TRANS }}
@stop
@section('content')

    <div ng-app="app" ng-controller="fTransactionController">

    <div class="row">
        <a class="title_hyper pull-left" href="/franchisee"><h1>{{ $FRANCHISE_TRANS }} <i class="fa fa-briefcase"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-primary" ng-cloak>
            <div class="panel-body">
                <div class="row" style="margin-top: -15px;">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Create panel
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label class="control-label">Customer</label>
                                    <select class="select form-control" ng-model="form.person_id">
                                        <option ng-value=""></option>
                                        @foreach($people::filterFranchiseePeople()->get() as $person)
                                            <option ng-value="{{$person->id}}">
                                                {{$person->cust_id}} {{$person->company}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label class="control-label">Date</label>
                                    <input type="text" name="collection_date" class="date form-control" datetimepicker options='{format: "YYYY-MM-DD"}' ng-model="form.collection_date" placeholder="Default: Today">
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-12 form-group">
                                    <label class="control-label">Time</label>
                                    <input type="text" name="collection_time" class="time form-control" datetimepicker options='{format: "hh:mm A"}' ng-model="form.collection_time" placeholder="Default: Now">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-sm-6 col-xs-6 form-group">
                                    <label class="control-label">Resettable Clocker</label>
                                    <input type="text" name="digital_clock" class="form-control" ng-model="form.digital_clock" placeholder="Numbers Only">
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-6 form-group">
                                    <label class="control-label">Accumulative Clocker</label>
                                    <input type="text" name="analog_clock" class="form-control" ng-model="form.analog_clock" placeholder="Numbers Only">
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12 form-group">
                                    <label class="control-label">Total $ Collected</label>
                                    <input type="text" name="total" class="form-control" ng-model="form.total" placeholder="Numbers Only">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                    <label class="control-label">Remarks</label>
                                    <textarea class="form-control" rows="2" ng-model="form.remarks"></textarea>
                                </div>
                            </div>
                            @if(!auth()->user()->hasRole('franchisee'))
                            <div class="row">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    {!! Form::label('franchisee_id', 'Franchisee', ['class'=>'control-label search-title']) !!}
                                    {!! Form::select('franchisee_id', [''=>'All']+$users::filterUserFranchise()->pluck('name', 'id')->all(), null, ['id'=>'franchisee_id',
                                        'class'=>'selectall form-control',
                                        'ng-model'=>'search.franchisee_id',
                                        'ng-change' => 'searchDB()'
                                        ])
                                    !!}
                                </div>
                                <span ng-if="formErrors['franchisee_id']" class="help-block" style="color:red;">
                                  <ul class="row">
                                      <li style="color:red;">@{{ formErrors['franchisee_id'][0] }}</li>
                                  </ul>
                                </span>
                            </div>
                            @endif
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
                        {!! Form::label('invoice', 'Ref #', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('invoice', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.id',
                                                            'ng-change'=>'searchDB()',
                                                            'placeholder'=>'Inv Num',
                                                            'ng-model-options'=>'{ debounce: 500 }'
                                                        ]) !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('id', 'ID', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.cust_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Cust ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
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
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('collection_from', 'Date From', ['class'=>'control-label search-title']) !!}
                        <div class="input-group">
                            <datepicker>
                                <input
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Date From"
                                    ng-model = "search.collection_from"
                                    ng-change = "collectionFromChanged(search.collection_from)"
                                />
                            </datepicker>
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('collection_from', search.collection_from)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('collection_from', search.collection_from)"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('collection_to', 'Date To', ['class'=>'control-label search-title']) !!}
                        <div class="input-group">
                            <datepicker>
                                <input
                                    type = "text"
                                    class = "form-control input-sm"
                                    placeholder = "Date To"
                                    ng-model = "search.collection_to"
                                    ng-change = "collectionToChanged(search.collection_to)"
                                />
                            </datepicker>
                            <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('collection_to', search.collection_to)"></span>
                            <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('collection_to', search.collection_to)"></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
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

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
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
                                <td data-tableexport-display="always">Total $ Collected</td>
                                <td data-tableexport-display="always" class="text-right">@{{total_vend_amount | currency: "": 2}}</td>
                            </tr>
                            <tr class="hidden" data-tableexport-display="always">
                                <td></td>
                            </tr>
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('ftransaction_id')">
                                    Ref #
                                    <span ng-if="search.sortName == 'ftransaction_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'ftransaction_id' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-2 text-center">
                                    <a href="" ng-click="sortTable('cust_id')">
                                    Customer
                                    <span ng-if="search.sortName == 'cust_id' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'cust_id' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('collection_datetime')">
                                    Date
                                    <span ng-if="search.sortName == 'collection_datetime' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'collection_datetime' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    Time
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('digital_clock')">
                                    Resettable Clock
                                    <span ng-if="search.sortName == 'digital_clock' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'digital_clock' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('analog_clock')">
                                    Accumulative Clock
                                    <span ng-if="search.sortName == 'analog_clock' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'analog_clock' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('sales')">
                                    Sales (pcs)
                                    <span ng-if="search.sortName == 'sales' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'sales' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('total')">
                                    $ Collected
                                    <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1 text-center">
                                    <a href="" ng-click="sortTable('updated_by')">
                                    Updated By
                                    <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-2 text-center">
                                    <a href="" ng-click="sortTable('remarks')">
                                    Remarks
                                    <span ng-if="search.sortName == 'remarks' && !search.sortBy" class="fa fa-caret-down"></span>
                                    <span ng-if="search.sortName == 'remarks' && search.sortBy" class="fa fa-caret-up"></span>
                                </th>
                                <th class="col-md-1"></th>
                            </tr>
                            <tbody>
                                <tr dir-paginate="ftransaction in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                    <td class="col-md-1 text-center">
                                        @{{ $index + indexFrom }}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        {{-- <a href="/franchisee/@{{ ftransaction.id }}/edit"> --}}
                                            @{{ftransaction.user_code}} @{{ftransaction.ftransaction_id}}
                                        {{-- </a> --}}
                                    </td>
                                    <td class="col-md-2 text-left">
                                        <a href="/person/@{{ ftransaction.person_id }}">
                                            @{{ftransaction.cust_id}} - @{{ ftransaction.cust_id[0] == 'D' || ftransaction.cust_id[0] == 'H' ? ftransaction.name : ftransaction.company }}
                                        </a>
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{ ftransaction.collection_date }}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{ ftransaction.collection_time }}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{ ftransaction.digital_clock }}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{ ftransaction.analog_clock }}
                                    </td>
                                    <td class="col-md-1 text-right">
                                        @{{ ftransaction.sales }}
                                    </td>
                                    <td class="col-md-1 text-right">
                                        @{{ ftransaction.total }}
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @{{ ftransaction.updated_by }}
                                    </td>
                                    <td class="col-md-2 text-left">
                                        <textarea name="remarks[@{{ftransaction.id}}]" class="form-control" style='min-width: 160px; align-content: left; font-size: 12px;' rows="2" ng-model="ftransaction.remarks" ng-change="changeRemarks(ftransaction.id, ftransaction.remarks)" ng-model-options="{ debounce: 600 }"></textarea>
                                    </td>
                                    <td class="col-md-1 text-center">
                                        @if(!auth()->user()->hasRole('driver'))
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

    <script src="/js/franchisee_index.js"></script>
@stop