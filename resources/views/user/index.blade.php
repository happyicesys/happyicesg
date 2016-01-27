@extends('template')
@section('title')
{{ $USER_TITLE }}
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/user"><h1>{{ $USER_TITLE }} <i class="fa fa-user"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app">
    <div class="panel-heading">
            <ul class="nav nav-pills nav-justified" role="tablist">

                <li class="active"><a href="#data" role="tab" data-toggle="tab">User Data</a></li>
            </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">

            <div class="tab-pane active" id="data">

                <div ng-controller="userController">

                    <div class="panel panel-default">
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
                            <div style="padding-bottom: 10px">
                                <label for="search_id" class="search">Search ID:</label>
                                <input type="text" ng-model="search.id">
                                <label for="search_name" class="search" style="padding-left: 10px">Name:</label>
                                <input type="text" ng-model="search.name">                    
                                <label for="search_contact" class="search" style="padding-left: 10px">Contact:</label>
                                <input type="text" ng-model="search.contact">

                            </div>
                            <div class="table-responsive">
                                <table class="table table-list-search table-hover table-bordered">
                                    <tr style="background-color: #DDFDF8">
                                        <th class="col-md-1">
                                            #
                                        </th>                    
                                        <th class="col-md-1">
                                            <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                                            ID
                                            <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                                            <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>                            
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
                                            <td class="col-md-1">{{ $USER_PREFIX }}@{{ user.id }}</td>
                                            <td class="col-md-2">@{{ user.name }}</td>
                                            <td class="col-md-2">@{{ user.username }}</td>
                                            <td class="col-md-2">@{{ user.contact }}</td>
                                            <td class="col-md-2">@{{ user.email }}</td>
                                            <td class="col-md-2">


                                            <a href="/user/@{{ user.id }}/edit" class="btn btn-sm btn-primary">Edit</a>

                                            @can('delete_user')
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(user.id)">Delete</button>
                                            @endcan
                                            </td>
                                        </tr>
                                        <tr ng-show="(users | filter:search).length == 0 || ! users.length">
                                            <td colspan="6">No Records Found</td>
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
            </div>        

    </div>
</div>          

<script src="/js/user.js"></script>              
@stop