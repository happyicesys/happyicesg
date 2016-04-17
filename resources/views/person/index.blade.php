@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/person"><h1>{{ $PERSON_TITLE }} <i class="fa fa-briefcase"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="personController">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_panel_title">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='50'">
                          <option ng-value="10">10</option>
                          <option ng-value="30">30</option>
                          <option ng-value="50">50</option>
                          <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        @cannot('transaction_view')
                        <a href="/person/create" class="btn btn-success">+ New {{ $PERSON_TITLE }}</a>
                        <a href="/onlineprice/create" class="btn btn-primary">+ Ecommerce Price Setup</a>
                        @endcannot
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('cust_id', 'ID:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('cust_id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.cust_id', 'placeholder'=>'ID']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('company', 'Company:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('company', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.company', 'placeholder'=>'Company']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('contact', 'Contact:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('contact', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.contact', 'placeholder'=>'Contact']) !!}
                    </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-6">
                        {!! Form::label('active', 'Active:', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('active', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.active', 'placeholder'=>'Active']) !!}
                    </div>
                </div>

                <div class="row">
                    <div style="padding: 0px 0px 10px 15px">
                        <button class="btn btn-primary" ng-click="exportData()">Export Excel</button>
                    </div>
                </div>
                <div class="table-responsive" id="exportable">
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
                                Company
                                <span ng-show="sortType == 'company' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'company' && sortReverse" class="fa fa-caret-up"></span>
                                </a>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                Att. To
                                <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
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
                                <a href="" ng-click="sortType = 'active'; sortReverse = !sortReverse">
                                Active
                                <span ng-show="sortType == 'active' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'active' && sortReverse" class="fa fa-caret-up"></span>
                            </th>
{{--
                            <th class="col-md-1 text-center">
                                Action
                            </th>   --}}
                        </tr>

                        <tbody>
                            <tr dir-paginate="person in people | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                <td class="col-md-1 text-center">@{{ number }} </td>
                                <td class="col-md-1">@{{ person.cust_id }}</td>
                                <td class="col-md-2">
                                    <a href="/person/@{{ person.id }}/edit">
                                    @{{ person.company }}
                                    </a>
                                </td>
                                <td class="col-md-1">@{{ person.name }}</td>
                                <td class="col-md-2">
                                    @{{ person.contact }}
                                    <span ng-show="person.alt_contact.length > 0">
                                    / @{{ person.alt_contact }}
                                    </span>
                                </td>
                                <td class="col-md-3">@{{ person.del_address }}</td>
                                <td class="col-md-1 text-center">@{{ person.del_postcode }}</td>
                                <td class="col-md-1 text-center">@{{ person.active }}</td>
{{--                                 <td class="col-md-1 text-center">
                                    <a href="/person/@{{ person.id }}/edit" class="btn btn-sm btn-primary">Profile</a>
                                </td> --}}
                            </tr>
                            <tr ng-show="(people | filter:search).length == 0 || ! people.length">
                                <td colspan="8" class="text-center">No Records Found</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(people | filter:search).length}} of @{{people.length}} entries</label>
                </div>
        </div>
    </div>

    <script src="/js/person.js"></script>
@stop