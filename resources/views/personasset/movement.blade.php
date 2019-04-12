<div ng-controller="personassetMovementController">
    <div class="panel panel-primary" >
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="pull-left">
                            Asset Movement
                        </span>
{{--                         @if(auth()->user()->hasRole('admin'))
                        <span class="pull-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#personassetmovement_modal" ng-click="createPersonassetMovementModal()">
                                <i class="fa fa-plus"></i>
                                Manually Create Movement
                            </button>
                        </span>
                        @endif --}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('code', 'Code', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="code" class="form-control input-sm" ng-model="search.code" ng-change="searchDB()" placeholder="Code" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="name" class="form-control input-sm" ng-model="search.name" ng-change="searchDB()" placeholder="Name" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('brand', 'Brand', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="brand" class="form-control input-sm" ng-model="search.brand" ng-change="searchDB()" placeholder="Brand" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('serial_no', 'Serial No', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="serial_no" class="form-control input-sm" ng-model="search.serial_no" ng-change="searchDB()" placeholder="Serial No" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('from_location', 'From Location', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="from_location" class="form-control input-sm" ng-model="search.from_location" ng-change="searchDB()" placeholder="From Location" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('from_invoice', 'From Invoice #', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="from_invoice" class="form-control input-sm" ng-model="search.from_invoice" ng-change="searchDB()" placeholder="From Invoice" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('to_location', 'To Location', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="to_location" class="form-control input-sm" ng-model="search.to_location" ng-change="searchDB()" placeholder="To Location" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('to_invoice', 'To Invoice #', ['class'=>'control-label search-title']) !!}
                    <input type="text" name="to_invoice" class="form-control input-sm" ng-model="search.to_invoice" ng-change="searchDB()" placeholder="To Invoice" ng-model-options="{debounce: 500}">
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('datefrom', 'Date From', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "datefrom"
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Date From"
                                ng-model = "search.datefrom"
                                ng-change = "dateFromChange(search.datefrom)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('datefrom', search.datefrom)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('datefrom', search.datefrom)"></span>
                    </div>
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('dateto', 'Date To', ['class'=>'control-label search-title']) !!}
                    <div class="input-group">
                        <datepicker>
                            <input
                                name = "dateto"
                                type = "text"
                                class = "form-control input-sm"
                                placeholder = "Date To"
                                ng-model = "search.dateto"
                                ng-change = "dateToChange(search.dateto)"
                            />
                        </datepicker>
                        <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('dateto', search.dateto)"></span>
                        <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('dateto', search.dateto)"></span>
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

            <div class="table-responsive" id="exportable_personassetmovement" style="padding-top:20px;">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('code')">
                            Code
                            <span ng-if="search.sortName == 'code' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'code' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('name')">
                            Name
                            <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('brand')">
                            Brand
                            <span ng-if="search.sortName == 'brand' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'brand' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('serial_no')">
                            Serial No
                            <span ng-if="search.sortName == 'serial_no' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'serial_no' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('sticker')">
                            Sticker
                            <span ng-if="search.sortName == 'sticker' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'sticker' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('remarks')">
                            Comment
                            <span ng-if="search.sortName == 'remarks' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'remarks' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('last_address')">
                            From Location
                            <span ng-if="search.sortName == 'last_address' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'last_address' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('from_transaction')">
                            From Inv #
                            <span ng-if="search.sortName == 'from_transaction' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'from_transaction' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>

                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('datein')">
                            Date In
                            <span ng-if="search.sortName == 'datein' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'datein' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('weekin')">
                            Week In
                            <span ng-if="search.sortName == 'weekin' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'weekin' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('to_address')">
                            To Location
                            <span ng-if="search.sortName == 'to_address' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'to_address' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('to_transaction')">
                            To Inv #
                            <span ng-if="search.sortName == 'to_transaction' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'to_transaction' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('dateout')">
                            Date Out
                            <span ng-if="search.sortName == 'dateout' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'dateout' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('weekout')">
                            Week Out
                            <span ng-if="search.sortName == 'weekout' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'weekout' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="" ng-click="sortTable('total_week')">
                            Total Week
                            <span ng-if="search.sortName == 'total_week' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'total_week' && search.sortBy" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                            <td class="col-md-1 text-center">
                                @{{ $index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.code}}
                            </td>
                            <td class="col-md-2 text-left">
                                @{{data.name}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.brand}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.serial_no}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.sticker}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.remarks}}
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.from_location_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                <a href="/transaction/@{{ data.transaction_id }}/edit">
                                    @{{ data.transaction_id }}
                                </a>
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.datein}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.datein_week}}
                                <small>
                                    (@{{data.datein_year}})
                                </small>
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.to_location_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                <a href="/transaction/@{{ data.to_transaction_id }}/edit">
                                    @{{ data.to_transaction_id }}
                                </a>
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.dateout}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.dateout_week}}
                                <small>
                                    <span ng-if="data.dateout_year">(</span>@{{data.dateout_year}} <span ng-if="data.dateout_year">)</span>
                                </small>
                            </td>
                            <td class="col-md-1 text-center">
                                @{{getWeekDifference(data.datein, data.dateout)}}
                            </td>
                            <td class="col-md-1 text-center">
                                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('driver') or auth()->user()->hasRole('operation'))
                                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#personassetmovement_modal" ng-click="editPersonassetMovementModal(data)"><i class="fa fa-pencil-square-o"></i></button>
                                    <button class="btn btn-danger btn-sm" ng-click="removeEntry(data.id)"><i class="fa fa-times"></i></button>
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

    <div class="modal fade" id="personassetmovement_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    @{{form.id ? 'Edit Asset Movement' : 'Add Customer Asset'}}
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <b>
                                @{{form.code}} - @{{form.name}} - @{{form.brand}}
                            </b>

{{--
                            <label for="required" class="control-label" style="color:red;">*</label>
                            <ui-select ng-model="form.personasset_id" on-select="onSelected($item)">
                                <ui-select-match allow-clear="true">@{{$select.selected.code}} - @{{$select.selected.name}} - @{{$select.selected.brand}}</ui-select-match>
                                <ui-select-choices repeat="personassetcategory.personasset_id as personassetcategory in personassetcategories | filter: $select.search">
                                    <div ng-bind-html="personassetcategory.code + ' - ' + personassetcategory.name + ' - ' + personassetcategory.brand | highlight: $select.search"></div>
                                </ui-select-choices>
                            </ui-select> --}}
                        </div>
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Serial No
                            </label>
                            <input type="text" name="serial_no" class="form-control" ng-model="form.serial_no">
                        </div>
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Sticker
                            </label>
                            <input type="text" name="sticker" class="form-control" ng-model="form.sticker">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Comment
                            </label>
                            <textarea name="remarks" class="form-control" id="remarks" rows="3" ng-model="form.remarks"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="createPersonassetMovement()" data-dismiss="modal" ng-if="!form.id" ng-disabled="isFormValid()">Create</button>
                    <button type="button" class="btn btn-success" ng-click="updatePersonassetMovement()" data-dismiss="modal" ng-if="form.id" ng-disabled="isFormValid()">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>