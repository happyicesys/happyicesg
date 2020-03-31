<div ng-controller="custTagsController">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('name', 'Cust Category', ['class'=>'control-label search-title']) !!}
                    <label class="pull-right">
                        <input type="checkbox" name="exclude_custcategory" ng-model="search.exclude_custcategory" ng-true-value="'1'" ng-false-value="'0'" ng-change="searchDB()">
                        <span style="margin-top: 5px;">
                            Exclude
                        </span>
                    </label>
{{--
                    {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(),
                        null,
                        [
                            'class'=>'selectmultiple form-control',
                            'ng-model'=>'search.custcategory',
                            'multiple'=>'multiple',
                            'ng-change' => "searchDB()"
                        ])
                    !!} --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="padding-left: 15px;">
        <div class="col-md-6 col-sm-12 col-xs-12" style="padding-top: 20px;">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    {{-- <button class="btn btn-info" ng-click="searchDB($event)"><i class="fa fa-search"></i><span class="hidden-xs"></span> Search</button> --}}
                    <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                    <strong>
                        SubTotal
                    </strong>
                    <span ng-if="driver == 'technician'">
                        (of all of 051)
                    </span>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                    <strong>@{{ subtotal ? subtotal : 0.00 | currency: "": 2}}</strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                    <strong>
                        Extra Location Total
                    </strong>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                    <strong>@{{ extra_location_total ? extra_location_total : 0}}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;" ng-if="driver == 'driver'">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                    <strong>
                        Commission ($)
                    </strong>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                    <strong ng-if="search.driver">
                        @{{ totalcommission ? totalcommission : 0.00 | currency: "": 2}}
                    </strong>
                    <span ng-if="!search.driver">
                        Only Available when driver is selected
                    </span>
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

    <div id="exportable_daily_report" class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
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
                    <a href="" ng-click="sortTable('transactions.delivery_day')">
                    Delivery Day
                    <span ng-if="search.sortName == 'transactions.delivery_day' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'transactions.delivery_day' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('driver')">
                    Delivered By
                    <span ng-if="search.sortName == 'driver' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'driver' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('total')">
                    Amount
                    <span ng-if="search.sortName == 'total' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'total' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('location_count')">
                    Location #
                    <span ng-if="search.sortName == 'location_count' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'location_count' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('extra_location')">
                    Extra Location #
                    <span ng-if="search.sortName == 'extra_location' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'extra_location' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="deal in alldata | itemsPerPage:itemsPerPage" pagination-id="dailyreport" total-items="totalCount" current-page="currentPage">
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ deal.delivery_date }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ deal.delivery_day }}
                    </td>
                    <td class="col-md-1 text-left">
                        @{{ deal.driver }}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ deal.total | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        <span ng-if="deal.submission_status == 3">
                            @{{ deal.location_count }}
                        </span>
                    </td>
                    <td class="col-md-1 text-right">
                        <span ng-if="deal.submission_status == 3">
                            @{{ deal.extra_location_count > 0 ? deal.extra_location_count : 0 }}
                        </span>
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