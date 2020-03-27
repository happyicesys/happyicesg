<div ng-controller="driverLocationCountController">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('date_from', 'Date From', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "date_from"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date From"
                            ng-model = "search.date_from"
                            ng-change = "dateFromChanged(search.date_from)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date_from', search.date_from)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date_from', search.date_from)"></span>
                </div>
            </div>
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('date_to', 'Date To', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "date_to"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Date To"
                            ng-model = "search.date_to"
                            ng-change = "dateToChanged(search.date_to)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('date_to', search.date_to)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('date_to', search.date_to)"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('driver', 'Delivered By', ['class'=>'control-label search-title']) !!}
                @if(Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician'))
                    {{-- {!! Form::text('driver', Auth::user()->name, ['class'=>'form-control input-sm', 'placeholder'=>'Delivered By', 'ng-model'=> 'search.driver', 'ng-init'=>"driverInit({{auth()->user()->name}})", 'readonly'=>'readonly']) !!} --}}
                    <input type="text" class="form-control input-sm" placeholder="Delivered By" ng-model="search.driver" ng-init="driverInit('{{auth()->user()->name}}')" readonly>
                @else
                    <select name="driver" class="form-control select" ng-model="search.driver" ng-change="searchDB()">
                        <option value="">All</option>
                        @foreach($users::orderBy('name')->get() as $user)
                            @if($user->hasRole('driver') or $user->hasRole('technician'))
                                <option value="{{$user->name}}">
                                    {{$user->name}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
    </div>

    <div class="row" style="padding-left: 15px;">
        <div class="col-md-8 col-sm-12 col-xs-12" style="padding-top: 20px;">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-12 text-right">
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

    <div id="exportable_driver_location_count" class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('transactions.delivery_date')">
                    Delivery Date
                    <span ng-if="search.sortName == 'transactions.delivery_date' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'transactions.delivery_date' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('driver')">
                    Delivered By
                    <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('location_count')">
                    Location Count
                    <span ng-if="search.sortName == 'location_count' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'location_count' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="dailyreport" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-left">
                        @{{ deal.delivery_date }}
                    </td>
                    <td class="col-md-1 text-left">
                        @{{ deal.driver }}
                    </td>
                    <td class="col-md-1 text-right">
                        {!! Form::text('location_count[@{{deal.location_count}}]', null, ['class'=>'text-right form-control', 'style'=>'min-width: 150px; align-content: left; font-size: 12px;', 'ng-model'=>'deal.location_count', 'ng-change'=>'updateLocationCount(deal)', 'ng-model-options'=>'{ debounce: 600 }', 'placeholder'=>'Numbers only']) !!}
                    </td>
                </tr>
                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="14" class="text-center">No Records Found</td>
                </tr>
            </tbody>
        </table>

        <div>
            <dir-pagination-controls max-size="5" pagination-id="dailyreport" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>