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
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
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
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
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
                        <th class="col-md-2 text-center">
                            <a href="" ng-click="sortTable('last_address')">
                            Last Location
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
                            <a href="" ng-click="sortTable('sticker')">
                            Sticker
                            <span ng-if="search.sortName == 'sticker' && !search.sortBy" class="fa fa-caret-down"></span>
                            <span ng-if="search.sortName == 'sticker' && search.sortBy" class="fa fa-caret-up"></span>
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
                            <td class="col-md-2 text-left">
                                @{{data.pickup_location_name}}
                            </td>
                            <td class="col-md-1 text-center">
                                <a href="/transaction/@{{ data.transaction_id }}/edit">
                                    @{{ data.transaction_id }}
                                </a>
                            </td>
                            <td class="col-md-1 text-left">
                                @{{data.sticker}}
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
                            <td class="col-md-1 text-center">
                                @{{data.dateout}}
                            </td>
                            <td class="col-md-1 text-center">
                                @{{data.dateout_week}}
                                <br>
                                <small>
                                    @{{data.dateout_year}}
                                </small>
                            </td>
                            <td class="col-md-1 text-left">
                                @{{getWeekDifference(data.datein, data.dateout)}}
                            </td>
                            <td class="col-md-1 text-center">
                                @if(auth()->user()->hasRole('admin'))
                                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#personasset_modal" ng-click="editPersonassetModal(data)"><i class="fa fa-pencil-square-o"></i></button>
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
                    @{{form.id ? 'Edit Customer Asset' : 'Add Customer Asset'}}
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Code
                            </label>
                            <label for="required" class="control-label" style="color:red;">*</label>
                            <input type="text" name="title" class="form-control" ng-model="form.code">
                        </div>
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Name
                            </label>
                            <label for="required" class="control-label" style="color:red;">*</label>
                            <input type="text" name="title" class="form-control" ng-model="form.name">
                        </div>
                        <div class="form-group col-md-4 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Brand
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.brand">
                        </div>

                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Size 1
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.size1">
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Size 2
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.size2">
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Weight
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.weight">
                        </div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-12">
                            <label class="control-label">
                                Capacity
                            </label>
                            <input type="text" name="title" class="form-control" ng-model="form.capacity">
                        </div>
                        <div class="form-group col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Specs 1
                            </label>
                            <textarea name="specs1" class="form-control" rows="5" ng-model="form.specs1"></textarea>
                            {{-- <input type="text" name="title" class="form-control" ng-model="form.capacity"> --}}
                        </div>
                        <div class="form-group col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Specs 2
                            </label>
                            <textarea name="specs2" class="form-control" rows="5" ng-model="form.specs2"></textarea>
                        </div>
                        <div class="form-group col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Specs 3
                            </label>
                            <textarea name="specs3" class="form-control" rows="5" ng-model="form.specs3"></textarea>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Customer
                            </label>
                            <label for="required" class="control-label" style="color:red;">*</label>
                            <ui-select ng-model="form.person_id" on-select="onSelected($item)">
                                <ui-select-match allow-clear="true">@{{$select.selected.cust_id}} - @{{$select.selected.company}}</ui-select-match>
                                <ui-select-choices repeat="person.id as person in people | filter: $select.search">
                                    <div ng-bind-html="person.cust_id + ' - ' + person.company | highlight: $select.search"></div>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="createPersonasset()" data-dismiss="modal" ng-if="!form.id" ng-disabled="isFormValid()">Create</button>
                    <button type="button" class="btn btn-success" ng-click="updatePersonasset()" data-dismiss="modal" ng-if="form.id" ng-disabled="isFormValid()">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>