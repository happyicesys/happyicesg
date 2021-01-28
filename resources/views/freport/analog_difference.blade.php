<div ng-controller="analogDifferenceController">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('cust_id', 'Customer ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('cust_id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.cust_id',
                                                        'placeholder'=>'ID',
                                                        'ng-change'=>'searchDB()',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                    !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('company', 'Customer Name', ['class'=>'control-label search-title']) !!}
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
        </div>


        <div class="row" style="padding-left: 15px; padding-top: 20px;">
            <div class="col-md-4 col-xs-12">

                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                    <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                @endif
                <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span>
            </div>
            <div class="col-md-4 col-md-offset-4 col-xs-12 text-right">
                <div class="row" style="padding-right:18px;">
                    <label>Display</label>
                    <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                        <option ng-value="100">100</option>
                        <option ng-value="200">200</option>
                        <option ng-value="All">All</option>
                    </select>
                    <label>per Page</label>
                </div>
                <div class="row">
                    <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                </div>
            </div>
        </div>

        <div class="table-responsive" id="exportable" style="padding-top:15px;">
            <table class="table table-list-search table-hover table-bordered">
                <tr class="hidden-xs" style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortType = 'cust_id'; sortReverse = !sortReverse">
                        ID
                        <span ng-show="sortType == 'cust_id' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'cust_id' && sortReverse" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortType = 'company'; sortReverse = !sortReverse">
                        ID Name
                        <span ng-show="sortType == 'company' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'company' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortType = 'stockin_analog'; sortReverse = !sortReverse">
                        Stock In Analog
                        <span ng-show="sortType == 'stockin_analog' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'stockin_analog' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortType = 'stockin_date'; sortReverse = !sortReverse">
                        Stock In Date
                        <span ng-show="sortType == 'stockin_date' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'stockin_date' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortType = 'collection_analog'; sortReverse = !sortReverse">
                        Collection Analog
                        <span ng-show="sortType == 'collection_analog' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'collection_analog' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortType = 'collection_date'; sortReverse = !sortReverse">
                        Collection Date
                        <span ng-show="sortType == 'collection_date' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'collection_date' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortType = 'difference_analog'; sortReverse = !sortReverse">
                        Difference <br>
                        (Stock - Collection)
                        <span ng-show="sortType == 'difference_analog' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'difference_analog' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                </tr>

                <tbody>
                    <tr class="hidden-xs" dir-paginate="person in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage" pagination-id="analog_difference">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-1">
                            <a href="/person/@{{ person.id }}/edit">
                            @{{ person.cust_id }}
                            </a>
                        </td>
                        <td class="col-md-2">
                            @{{ person.company }}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{person.stockin_analog}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{person.stockin_date}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{person.collection_analog}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{person.collection_date}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{person.difference_analog}}
                        </td>
                    </tr>
                    <tr class="hidden-lg hidden-md hidden-sm" style="font-size: 14px;" dir-paginate="person in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage" pagination-id="analog_difference">
                        <td class="col-xs-12 text-left">
                            @{{ $index + indexFrom }}.
                            <a href="/person/@{{ person.id }}/edit">
                            @{{ person.cust_id }}
                            </a>
                            <br>
                            <span class="col-xs-12" style="font-size: 16px;">
                                <strong>
                                    @{{ person.company }}
                                </strong>
                            </span>
                            <br>
                            <span class="col-xs-12 row">
                                <span class="col-xs-6">
                                    Stock In Analog:
                                </span>
                                <span class="col-xs-6">
                                    @{{person.stockin_analog}}
                                </span>
                            </span>
                            <br>
                            <span class="col-xs-12 row">
                                <span class="col-xs-6">
                                    Stock In Date:
                                </span>
                                <span class="col-xs-6">
                                    @{{person.stockin_date}}
                                </span>
                            </span>
                            <br>
                            <span class="col-xs-12 row">
                                <span class="col-xs-6">
                                    Collection Analog:
                                </span>
                                <span class="col-xs-6">
                                    @{{person.collection_analog}}
                                </span>
                            </span>
                            <br>
                            <span class="col-xs-12 row">
                                <span class="col-xs-6">
                                    Collection Date:
                                </span>
                                <span class="col-xs-6">
                                    @{{person.collection_date}}
                                </span>
                            </span>
                            <br>
                            <span class="col-xs-12 row">
                                <span class="col-xs-6">
                                    Analog Diff:
                                </span>
                                <span class="col-xs-6">
                                    <strong>
                                        @{{person.difference_analog}}
                                    </strong>
                                </span>
                            </span>
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <dir-pagination-controls pagination-id="analog_difference" max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>