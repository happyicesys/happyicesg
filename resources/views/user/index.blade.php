@extends('template')
@section('title')
{{ $USER_TITLE }}
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/user"><h1>{{ $USER_TITLE }} <i class="fa fa-user"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app" ng-cloak>
    <div class="panel-heading">
        <ul class="nav nav-pills nav-justified" role="tablist">
            <li class="active"><a href="#data" role="tab" data-toggle="tab">User Data</a></li>
            <li><a href="#freezer" role="tab" data-toggle="tab">Freezer</a></li>
            <li><a href="#accessory" role="tab" data-toggle="tab">Accessory</a></li>
            <li><a href="#payterm" role="tab" data-toggle="tab">Pay Term</a></li>
            <li><a href="#cust_cat" role="tab" data-toggle="tab">Customer Category</a></li>
            <li><a href="#cust_tags" role="tab" data-toggle="tab">Customer Tags</a></li>
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            {{-- first element --}}
            <div class="tab-pane active" id="data" >
                <div class="panel panel-default" ng-controller="userController">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
                                  <option>10</option>
                                  <option>20</option>
                                  <option>30</option>
                                </select>
                                <label for="display_num2" style="padding-right: 20px">per Page</label>
                            </div>

                            <div class="pull-right">
                                @can('create_user')
                                <a href="/user/create" class="btn btn-success">+ New {{ $USER_TITLE }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('id', 'ID:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.id', 'placeholder'=>'ID']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('contact', 'Contact:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('contact', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.contact', 'placeholder'=>'Contact']) !!}
                            </div>
                        </div>

                        <div class="row"></div>

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1">
                                        #
                                    </th>
                                    <th class="col-md-2">
                                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2">
                                        <a href="#" ng-click="sortType = 'username'; sortReverse = !sortReverse">
                                        Username
                                        <span ng-show="sortType == 'username' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'username' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2">
                                        Roles
                                    </th>
                                    <th class="col-md-1">
                                        Contact
                                    </th>
                                    <th class="col-md-2">
                                        Email
                                    </th>
                                     <th class="col-md-2">
                                        Action
                                    </th>
                                </tr>

                                <tbody>

                                     <tr dir-paginate="user in users | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage" pagination-id="user" current-page="currentPage" ng-controller="repeatController">
                                        <td class="col-md-1">@{{ number }} </td>
                                        <td class="col-md-2">@{{ user.name }}</td>
                                        <td class="col-md-2">@{{ user.username }}</td>
                                        <td class="col-md-2">
                                            <ul>
                                                <li ng-repeat='(key, value) in user.roles'>
                                                    <span>@{{value.label}}</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="col-md-2">@{{ user.contact }}</td>
                                        <td class="col-md-2">@{{ user.email }}</td>
                                        <td class="col-md-2 text-center">
                                            <a href="/user/@{{ user.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                            @can('delete_user')
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(user.id)">Delete</button>
                                            @endcan
                                        </td>
                                    </tr>
                                    <tr ng-show="(users | filter:search).length == 0 || ! users.length">
                                        <td colspan="7" class="text-center">No Records Found</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-footer">
                          <dir-pagination-controls pagination-id="user" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                          <label class="pull-right totalnum" for="totalnum">Showing @{{(users | filter:search).length}} of @{{users.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of first element--}}
            {{-- second element --}}
            <div class="tab-pane" id="freezer">
                <div class="panel panel-default" ng-controller="userController">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage2" ng-init="itemsPerPage2='10'">
                                  <option>10</option>
                                  <option>20</option>
                                  <option>30</option>
                                </select>
                                <label for="display_num2" style="padding-right: 20px">per Page</label>
                            </div>

                            <div class="pull-right">
                                <a href="/freezer/create" class="btn btn-success">+ New Freezer</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
                            </div>
                        </div>

                        <div class="row"></div>

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-9">
                                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Action
                                    </th>
                                </tr>

                                <tbody>
                                     <tr dir-paginate="freezer in freezers | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage2" pagination-id="freezer" current-page="currentPage2" ng-controller="repeatController2">
                                        <td class="col-md-1 text-center">@{{ number }} </td>
                                        <td class="col-md-9">@{{ freezer.name }}</td>
                                        <td class="col-md-2 text-center">
                                            <a href="/freezer/@{{ freezer.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete2(freezer.id)">Delete</button>
                                        </td>
                                    </tr>
                                    <tr ng-show="(freezers | filter:search).length == 0 || ! freezers.length">
                                        <td colspan="6" class="text-center">No Records Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-footer">
                          <dir-pagination-controls pagination-id="freezer" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                          <label class="pull-right totalnum" for="totalnum">Showing @{{(freezers | filter:search).length}} of @{{freezers.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of second element --}}
            {{-- start of third element --}}
            <div class="tab-pane" id="accessory">
                <div class="panel panel-default" ng-controller="userController">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage3" ng-init="itemsPerPage3='10'">
                                  <option>10</option>
                                  <option>20</option>
                                  <option>30</option>
                                </select>
                                <label for="display_num2" style="padding-right: 20px">per Page</label>
                            </div>

                            <div class="pull-right">
                                <a href="/accessory/create" class="btn btn-success">+ New Accessory</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
                            </div>
                        </div>

                        <div class="row"></div>

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-9">
                                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Action
                                    </th>
                                </tr>

                                <tbody>
                                     <tr dir-paginate="accessory in accessories | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage3" pagination-id="accessory" current-page="currentPage3" ng-controller="repeatController3">
                                        <td class="col-md-1 text-center">@{{ number }} </td>
                                        <td class="col-md-9">@{{ accessory.name }}</td>
                                        <td class="col-md-2 text-center">
                                            <a href="/accessory/@{{ accessory.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete3(accessory.id)">Delete</button>
                                        </td>
                                    </tr>
                                    <tr ng-show="(accessories | filter:search).length == 0 || ! accessories.length">
                                        <td colspan="6" class="text-center">No Records Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-footer">
                          <dir-pagination-controls pagination-id="accessory" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                          <label class="pull-right totalnum" for="totalnum">Showing @{{(accessories | filter:search).length}} of @{{accessories.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of third element--}}
            {{-- start of fourth element --}}
            <div class="tab-pane" id="payterm">
                <div class="panel panel-default" ng-controller="userController">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage4" ng-init="itemsPerPage4='10'">
                                  <option>10</option>
                                  <option>20</option>
                                  <option>30</option>
                                </select>
                                <label for="display_num2" style="padding-right: 20px">per Page</label>
                            </div>

                            <div class="pull-right">
                                <a href="payterm/create" class="btn btn-success">+ New PayTerm</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
                            </div>
                        </div>

                        <div class="row"></div>

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-7">
                                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2">
                                        Desc
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Action
                                    </th>
                                </tr>

                                <tbody>
                                     <tr dir-paginate="payterm in payterms | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage4" pagination-id="payterm" current-page="currentPage4" ng-controller="repeatController4">
                                        <td class="col-md-1 text-center">@{{ number }} </td>
                                        <td class="col-md-7">@{{ payterm.name }}</td>
                                        <td class="col-md-2">@{{ payterm.desc }}</td>
                                        <td class="col-md-2 text-center">
                                            <a href="/payterm/@{{ payterm.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete4(payterm.id)">Delete</button>
                                        </td>
                                    </tr>
                                    <tr ng-show="(payterms | filter:search).length == 0 || ! payterms.length">
                                        <td colspan="6" class="text-center">No Records Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-footer">
                          <dir-pagination-controls pagination-id="payterm" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                          <label class="pull-right totalnum" for="totalnum">Showing @{{(payterms | filter:search).length}} of @{{payterms.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of fourth element--}}

            {{-- fifth element --}}
            <div class="tab-pane" id="cust_cat" ng-controller="custCategoryController">
                @include('user.custcat_template')
            </div>
            {{-- end of fifth element --}}

            <div class="tab-pane" id="cust_tags" ng-controller="custTagsController">
                @include('user.cust_tags_template')
            </div>
    </div>
</div>

<script src="/js/user.js"></script>
@stop