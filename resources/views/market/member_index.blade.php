@extends('template')
@section('title')
Members
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/market/member"><h1>Members <i class="fa fa-sitemap"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app" ng-controller="userController">
    <div class="panel-heading">
        <ul class="nav nav-pills nav-justified" role="tablist">
            <li class="active"><a href="#member" role="tab" data-toggle="tab">Members</a></li>
            <li><a href="#profile" role="tab" data-toggle="tab">Profile</a></li>
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            {{-- first element --}}
            <div class="tab-pane active" id="member">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-left display_num">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage" ng-init="itemsPerPage='50'">
                                    <option ng-value="10">10</option>
                                    <option ng-value="30">30</option>
                                    <option ng-value="50">50</option>
                                    <option ng-value="All">All</option>
                                </select>
                                <label for="display_num" style="padding-right: 20px">per Page</label>
                            </div>

                            <div class="pull-right">
                                <a href="/market/member/create" class="btn btn-success">+ New Member</a>
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
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                                        ID
                                        <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Att To
                                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Contact
                                    </th>
                                    <th class="col-md-3 text-center">
                                        Delivery Add
                                    </th>
                                    <th class="col-md-1 text-center">
                                        Active
                                    </th>
                                     <th class="col-md-2">
                                        Action
                                    </th>
                                </tr>

                                <tbody>

                                    <tr dir-paginate="user in users | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage" pagination-id="user" current-page="currentPage" ng-controller="repeatController">
                                        <td class="col-md-1">@{{ number }} </td>
                                        <td class="col-md-1">{{ $USER_PREFIX }}@{{ user.id }}</td>
                                        <td class="col-md-2">@{{ user.name }}</td>
                                        <td class="col-md-2">@{{ user.username }}</td>
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
                <div class="panel panel-default">
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
        </div>
    </div>
</div>

<script src="/js/user.js"></script>
@stop