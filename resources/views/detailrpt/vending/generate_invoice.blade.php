<div ng-controller="generateInvoiceController">
{!! Form::open(['id'=>'submit_generate', 'method'=>'POST', 'action'=>['VendingController@batchGenerateVendingInvoice'], 'onsubmit'=>'return verifySubmit()']) !!}
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
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
        <div class="col-md-4 col-sm-6 col-xs-12" ng-show="!search.begin_date && !search.end_date">
            <div class="form-group">
                {!! Form::label('current_month', 'Month', ['class'=>'control-label search-title']) !!}
                <select class="select form-control" name="current_month" ng-model="search.current_month" ng-init="search.current_month = getPreviousMonthYear()" ng-change="searchDB()">
                    <option value="">All</option>
                    @foreach($month_options as $key => $value)
                        <option value="{{$key}}" selected="{{Carbon\Carbon::today()->subMonth()->month.'-'.Carbon\Carbon::today()->subMonth()->year ? 'selected' : ''}}">{{$value}}</option>
                    @endforeach
                </select>
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
        <div class="col-md-4 col-sm-6 col-xs-12">
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
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
{{--                 {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.custcategory',
                    'ng-change'=>'searchDB()'
                    ])
                !!} --}}
                <select name="custcategory[]" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                    <option value="">All</option>
                    @foreach($custcategories::orderBy('name')->get() as $custcategory)
                    <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
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
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('is_rental', 'Coorperate Method', ['class'=>'control-label search-title']) !!}
                {!! Form::select('is_rental', [''=>'All', 'Rental'=>'Rental Based', 'Profit'=>'Profit Sharing', 'Others'=>'Others'], null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.is_rental',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('is_active', 'Is Active', ['class'=>'control-label search-title']) !!}
                {!! Form::select('is_active', [''=>'All', 'Yes'=>'Yes', 'No'=>'No'], null,
                    [
                    'class'=>'select form-control',
                    'ng-model'=>'search.is_active',
                    'ng-change'=>'searchDB()'
                    ])
                !!}
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-md-3 col-sm-6 col-xs-12">
            {!! Form::label('begin_date', 'Begin Date', ['class'=>'control-label search-title']) !!}
            <div class="input-group">
                <datepicker>
                    <input
                        name = "begin_date"
                        type = "text"
                        class = "form-control input-sm"
                        placeholder = "Begin Date"
                        ng-model = "search.begin_date"
                        ng-change = "beginDateChanged(search.begin_date)"
                    />
                </datepicker>
                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('begin_date', search.begin_date)"></span>
                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('begin_date', search.begin_date)"></span>
            </div>
        </div>
        <div class="form-group col-md-3 col-sm-6 col-xs-12">
            {!! Form::label('end_date', 'End Date', ['class'=>'control-label search-title']) !!}
            <div class="input-group">
                <datepicker>
                    <input
                        name = "end_date"
                        type = "text"
                        class = "form-control input-sm"
                        placeholder = "End Date"
                        ng-model = "search.end_date"
                        ng-change = "endDateChanged(search.end_date)"
                    />
                </datepicker>
                <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('end_date', search.end_date)"></span>
                <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('end_date', search.end_date)"></span>
            </div>
        </div>
        <div class="form-group col-md-3 col-sm-6 col-xs-12">
            <small>* For analysis purpose only, cannot be used to batch generate</small> <br>
            <button class="btn btn-default" ng-click="clearDates($event)">Clear Dates</button>
        </div>
    </div>
</div>

<div class="row" style="padding-left: 15px;">
    <div class="col-md-6 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
            <button ng-disabled="search.begin_date || search.end_date" title="@{{(search.begin_date || search.end_date) ? 'Please clear the dates to enable batch generate': ''}}" type="submit" class="btn btn-danger" form="submit_generate" name="submit_generate" value="submit_generate" ><i class="fa fa-download"></i><span class="hidden-xs"></span> Batch Generate Invoice</button>
            <span ng-show="spinner"> <i style="color:red;" class="fa fa-spinner fa-2x fa-spin"></i></span>
        </div>
        <div class="row" style="padding-top: 10px;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <span class="badge" style="background-color: #f68080;">&nbsp;</span>
                <span>Total Profit Sharing jump more than 50% to last month</span>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <span class="badge" style="background-color: yellow;">&nbsp;</span>
                <span>More than $100 ice cream melted</span>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <span class="badge" style="background-color: #98fb98;">&nbsp;</span>
                <span>No data for current month</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-12 col-xs-12 text-right">
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

<div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Machine Qty:
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totalCount ? totalCount : 0}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total Sales # Ice Cream:
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_sales ? total_sales : 0.00 | currency:"":0}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Avg # Ice Cream/ machine:
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totalCount ? total_sales/totalCount : 0.00 | currency:"":0}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Total Sales ($):
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_sales_figure ? total_sales_figure : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Avg Sales ($)/ machine:
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ totalCount ? total_sales_figure/totalCount : 0.00 | currency: "": 2}}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12" style="padding-top: 20px;">
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
                Total Rental Paid ($):
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_rental ? total_rental : 0.00 | currency: "": 2}}</strong>
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
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                Gross Profit ($):
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right" style="border: thin black solid">
                <strong>@{{ total_gross_profit ? total_gross_profit : 0.00 | currency: "": 2}}</strong>
            </div>
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
                    Cat
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
                    Adj Rate (%)
                    <span ng-if="search.sortName == 'clocker_adjustment' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'clocker_adjustment' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('sales')">
                    # Ice Cream
                    <span ng-if="search.sortName == 'sales' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'sales' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('profit_sharing')">
                    Commission (per pcs)
                    <span ng-if="search.sortName == 'profit_sharing' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'profit_sharing' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('subtotal_sales')">
                    Sales ($)
                    <span ng-if="search.sortName == 'subtotal_sales' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'subtotal_sales' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('subtotal_profit_sharing')">
                    Total Profit Sharing ($)
                    <span ng-if="search.sortName == 'subtotal_profit_sharing' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'subtotal_profit_sharing' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('utility_subsidy')">
                    Utility Fees ($)
                    <span ng-if="search.sortName == 'utility_subsidy' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'utility_subsidy' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('vending_monthly_rental')">
                    Rental ($)
                    <span ng-if="search.sortName == 'vending_monthly_rental' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'vending_monthly_rental' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('subtotal_payout')">
                    Total Payout ($)
                    <span ng-if="search.sortName == 'subtotal_payout' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'subtotal_payout' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('subtotal_gross_profit')">
                    Gross Profit ($)
                    <span ng-if="search.sortName == 'subtotal_gross_profit' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'subtotal_gross_profit' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortTable('avg_selling_price')">
                    GP/ pcs ($)
                    <span ng-if="search.sortName == 'avg_selling_price' && !search.sortBy" class="fa fa-caret-down"></span>
                    <span ng-if="search.sortName == 'avg_selling_price' && search.sortBy" class="fa fa-caret-up"></span>
                </th>
            </tr>

            <tbody>
                <tr ng-repeat="person in absentlist" style="background-color: #98fb98;">
                    <td colspan="2"></td>
                    <td class="col-md-1 text-center">
                        @{{person.cust_id}}
                    </td>
                    <td class="col-md-1 text-left">
                        <a href="/person/@{{ person.id }}">
                            @{{ person.cust_id[0] == 'D' || person.cust_id[0] == 'H' ? person.name : person.company }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center">
                        @{{person.custcategory.name}}
                    </td>
                    <td colspan="18"></td>
                </tr>
                <tr dir-paginate="transaction in alldata | itemsPerPage:itemsPerPage" pagination-id="generate_invoice" total-items="totalCount" current-page="currentPage" ng-style="{'background-color': getRowColor(transaction)}">
                    <td class="col-md-1 text-center">
                        {!! Form::checkbox('checkbox[@{{transaction.person_id}}]') !!}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ $index + indexFrom }}
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ transaction.cust_id }}
                    </td>
                    <td class="col-md-1 text-left">
                        <a href="/person/@{{ transaction.person_id }}">
                            @{{ transaction.cust_id[0] == 'D' || transaction.cust_id[0] == 'H' ? transaction.name : transaction.company }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center">
                        @{{ transaction.custcategory }}
                    </td>
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
                        @{{ transaction.profit_sharing | currency: "": 2}} <small>(@{{transaction.profit_sharing_format}})</small>
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.subtotal_sales}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.subtotal_profit_sharing | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.utility_subsidy | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.vending_monthly_rental | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.subtotal_payout | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.subtotal_gross_profit | currency: "": 2}}
                    </td>
                    <td class="col-md-1 text-right">
                        @{{ transaction.avg_selling_price | currency: "": 2}}
                    </td>
                </tr>

                <tr ng-if="!alldata || alldata.length == 0">
                    <td colspan="20" class="text-center">No Records Found</td>
                </tr>

            </tbody>
        </table>

        <div>
              <dir-pagination-controls max-size="5" pagination-id="generate_invoice" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
    {!! Form::close() !!}
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