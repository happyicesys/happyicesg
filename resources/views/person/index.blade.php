@inject('custcategories', 'App\Custcategory')
@inject('profiles', 'App\Profile')

@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/person"><h1>{{ $PERSON_TITLE }} <i class="fa fa-users"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="personController">

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="pull-right">
                        @cannot('transaction_view')
                            <a href="/person/create" class="btn btn-sm btn-success">+ New {{ $PERSON_TITLE }}</a>
                            <a href="/onlineprice/create" class="btn btn-sm btn-default">+ Ecommerce Price Setup</a>
                            @if(auth()->user()->hasRole('admin'))
                                <a href="/pricematrix" class="btn btn-sm btn-default"><i class="fa fa-list"></i> Price Matrix</a>
                            @endif
                        @endcannot
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
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
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('custcategory', 'Category', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('custcategory', [''=>'All']+$custcategories::orderBy('name')->pluck('name', 'id')->all(), null,
                                [
                                'class'=>'select form-control',
                                'ng-model'=>'search.custcategory',
                                'ng-change'=>'searchDB()'
                                ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
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
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('contact', 'Contact', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('contact', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.contact',
                                                                'placeholder'=>'Contact',
                                                                'ng-change'=>'searchDB()',
                                                                'ng-model-options'=>'{ debounce: 500 }'
                                                            ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('active', 'Active', ['class'=>'control-label search-title']) !!}
                            {!! Form::text('active', null,
                                                            [
                                                                'class'=>'form-control input-sm',
                                                                'ng-model'=>'search.active',
                                                                'placeholder'=>'Active',
                                                                'ng-change'=>'searchDB()',
                                                                'ng-model-options'=>'{ debounce: 500 }',
                                                                'ng-init'=>'search.active = "Yes"'
                                                            ])
                            !!}
                        </div>
                        <div class="form-group col-md-2 col-sm-4 col-xs-6">
                            {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                            {!! Form::select('profile_id', [''=>'All']+$profiles::filterUserProfile()->pluck('name', 'id')->all(), null, ['id'=>'profile_id',
                                'class'=>'select form-control',
                                'ng-model'=>'search.profile_id',
                                'ng-change' => 'searchDB()'
                                ])
                            !!}
                        </div>
                    </div>
                </div>

                <div class="row" style="padding-left: 15px; padding-top: 20px;">
                    <div class="col-md-4 col-xs-12">
                        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
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
                </div>
                <div class="table-responsive" id="exportable" style="padding-top: 20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
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
                                <a href="" ng-click="sortType = 'custcategory'; sortReverse = !sortReverse">
                                Cat
                                <span ng-show="sortType == 'custcategory' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'custcategory' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                Att. To
                                <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'contact'; sortReverse = !sortReverse">
                                Contact
                                <span ng-show="sortType == 'contact' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'contact' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-3 text-center">
                                Delivery Add
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'del_postcode'; sortReverse = !sortReverse">
                                Postcode
                                <span ng-show="sortType == 'del_postcode' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'del_postcode' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'payterm'; sortReverse = !sortReverse">
                                Payterm
                                <span ng-show="sortType == 'payterm' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'payterm' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'active'; sortReverse = !sortReverse">
                                Active
                                <span ng-show="sortType == 'active' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'active' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                        </tr>

                        <tbody>
                            <tr dir-paginate="person in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                <td class="col-md-1">
                                    <a href="/person/@{{ person.id }}/edit">
                                    @{{ person.cust_id }}
                                    </a>
                                </td>
                                <td class="col-md-2">
                                    @{{ person.company }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ person.custcategory }}
                                </td>
                                <td class="col-md-1">@{{ person.name }}</td>
                                <td class="col-md-1">
                                    @{{ person.contact }}
                                    <span ng-show="person.alt_contact.length > 0">
                                    / @{{ person.alt_contact }}
                                    </span>
                                </td>
                                <td class="col-md-3">@{{ person.del_address }}</td>
                                <td class="col-md-1 text-center">@{{ person.del_postcode }}</td>
                                <td class="col-md-1 text-center">@{{person.payterm}}</td>
                                <td class="col-md-1 text-center">@{{ person.active }}</td>
                            </tr>
                            <tr ng-if="!alldata || alldata.length == 0">
                                <td colspan="14" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="panel-footer">
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                </div>
        </div>
    </div>

    <script src="/js/person.js"></script>
@stop