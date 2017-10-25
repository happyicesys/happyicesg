<div ng-controller="operationWorksheetController">
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
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                {!! Form::text('cust_id', null,
                                            [
                                                'class'=>'form-control input-sm',
                                                'ng-model'=>'search.cust_id',
                                                'placeholder'=>'Cust ID',
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
                {!! Form::label('id_prefix', 'ID Group', ['class'=>'control-label search-title']) !!}
                <select class="select form-group" name="id_prefix" ng-model="search.id_prefix" ng-change="searchDB()">
                    <option value="">All</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                    <option value="H">H</option>
                    <option value="R">R</option>
                    <option value="S">S</option>
                    <option value="V">V</option>
                    <option value="W">W</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('company', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.company',
                                                    'placeholder'=>'ID Name',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
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
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.status',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        <button type="submit" class="btn btn-danger" form="submit_generate" name="submit_generate" value="submit_generate" ><i class="fa fa-download"></i><span class="hidden-xs"></span> Batch Generate Invoice</button>
        <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total Sales # Ice Cream:
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_sales ? total_sales : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total Profit Sharing ($):
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_profit_sharing ? total_profit_sharing : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total Utility ($):
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_utility ? total_utility : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total Payout ($):
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_payout ? total_payout : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12 text-right">
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

    <div class="table-responsive" id="exportable_generate_invoice" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">

            {{-- hidden table for excel export --}}
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Sales # Ice Cream</td>
                <td data-tableexport-display="always" class="text-right">@{{total_sales | currency: "": 2}}</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Profit Sharing ($)</td>
                <td data-tableexport-display="always" class="text-right">@{{total_profit_sharing | currency: "": 2}}</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Utility ($)</td>
                <td data-tableexport-display="always" class="text-right">@{{total_utility | currency: "": 2}}</td>
            </tr>
            <tr class="hidden">
                <td></td>
                <td data-tableexport-display="always">Total Payout ($)</td>
                <td data-tableexport-display="always" class="text-right">@{{total_payout | currency: "": 2}}</td>
            </tr>
            <tr class="hidden" data-tableexport-display="always">
                <td></td>
            </tr>

            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    <input type="checkbox" id="checkAll" />
                </th>
                <th class="col-md-1 text-center">
                    #
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
                    <a href="" ng-click="sortTable('custcategories.id')">
                    Category
                    <span ng-if="search.sortName == 'custcategories.id' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'custcategories.id' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('begin_date')">
                    Begin Date
                    <span ng-if="search.sortName == 'begin_date' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'begin_date' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('begin_analog')">
                    Begin Analog Clocker
                    <span ng-if="search.sortName == 'begin_analog' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'begin_analog' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('end_date')">
                    End Date
                    <span ng-if="search.sortName == 'end_date' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'end_date' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('end_analog')">
                    End Analog Clocker
                    <span ng-if="search.sortName == 'end_analog' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'end_analog' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('clocker_delta')">
                    Clocker Delta
                    <span ng-if="search.sortName == 'clocker_delta' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'clocker_delta' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('clocker_adjustment')">
                    Clocker Adjustment (%)
                    <span ng-if="search.sortName == 'clocker_adjustment' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'clocker_adjustment' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('sales')">
                    Sales # Ice Cream
                    <span ng-if="search.sortName == 'sales' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'sales' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('profit_sharing')">
                    Profit Sharing ($/piece)
                    <span ng-if="search.sortName == 'profit_sharing' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'profit_sharing' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('subtotal_profit_sharing')">
                    Total Profit Sharing ($)
                    <span ng-if="search.sortName == 'subtotal_profit_sharing' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'subtotal_profit_sharing' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('utility_subsidy')">
                    Utility Subsidy ($)
                    <span ng-if="search.sortName == 'utility_subsidy' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'utility_subsidy' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('subtotal_payout')">
                    Total Payout ($)
                    <span ng-if="search.sortName == 'subtotal_payout' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'subtotal_payout' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage" pagination-id="generate_invoice" total-items="totalCount" current-page="currentPage" ng-style="transaction.begin_date == transaction.end_date ? {'background-color': 'yellow'} : ''">
                    <td class="col-md-1 text-center">
                        {!! Form::checkbox('checkbox[@{{transaction.person_id}}]') !!}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ transaction.cust_id }}
                    </td>
                    <td class="col-md-1 text-center">
                        <a href="/person/@{{ transaction.person_id }}">
                            @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ transaction.custcategory }}
                    </td>
                    {{-- status by color --}}
{{--                     <td class="col-md-1 text-center" style="color: red;" ng-if="transaction.status == 'Pending'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: orange;" ng-if="transaction.status == 'Confirmed'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: green;" ng-if="transaction.status == 'Delivered'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: black; background-color:orange;" ng-if="transaction.status == 'Verified Owe'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" style="color: black; background-color:green;" ng-if="transaction.status == 'Verified Paid'">
                        @{{ transaction.status }}
                    </td>
                    <td class="col-md-1 text-center" ng-if="transaction.status == 'Cancelled'">
                        <span style="color: white; background-color: red;" > @{{ transaction.status }} </span>
                    </td> --}}
                    {{-- status by color ended --}}
                    <td class="col-md-1 text-center">
                        @{{ transaction.begin_date | delDate: "yyyy-MM-dd"}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.begin_analog}}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ transaction.end_date | delDate: "yyyy-MM-dd"}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.end_analog}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.clocker_delta}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.clocker_adjustment}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.sales}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.profit_sharing | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.subtotal_profit_sharing | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.utility_subsidy | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.subtotal_payout | currency: "": 2}}
                    </td>
                </tr>

                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="18" class="text-center">No Records Found</td>
                </tr>

            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="generate_invoice" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>

<script>
    function verifySubmit() {

        if(confirm('Are you sure to batch generate invoice(s)?')) {
            return true;
        }else {
            return false;
        }
    }
</script>