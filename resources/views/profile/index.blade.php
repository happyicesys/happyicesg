@extends('template')
@section('title')
{{ $PROFILE_TITLE }}
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/profile"><h1> {{ $PROFILE_TITLE }} <i class="fa fa-building"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="profileController">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_panel_title">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='30'">
                          <option ng-value="10">10</option>
                          <option ng-value="20">20</option>
                          <option ng-value="30">30</option>
                          <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        <a href="/profile/create" class="btn btn-success">+ New {{ $PROFILE_TITLE }}</a>                        
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div style="padding-bottom: 10px">
                    <label for="search_name" class="search">Search Name:</label>
                    <input type="text" ng-model="search.name">
                    <label for="search_company" class="search" style="padding-left: 10px">ROC No:</label>
                    <input type="text" ng-model="search.roc_no">
                </div>
                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>                    
                            <th class="col-md-4 text-center">
                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                Name
                                <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                </a>                                                        
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                ROC No
                                <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                </a>                            
                            </th>
                            <th class="col-md-3 text-center">
                                Address
                            </th>                                                
                            <th class="col-md-2 text-center">
                                Action
                            </th>                                                                                                
                        </tr>

                        <tbody>
                            <tr dir-paginate="profile in profiles | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                <td class="col-md-1 text-center">@{{ number }} </td>
                                <td class="col-md-4 text-center">@{{ profile.name }}</td>
                                <td class="col-md-2 text-center">@{{ profile.roc_no }}</td>
                                <td class="col-md-3">@{{ profile.address }}</td>
                                <td class="col-md-2 text-center">
                                    <a href="/profile/@{{ profile.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(profile.id)">Delete</button>
                                </td>
                            </tr>
                            <tr ng-show="(profiles | filter:search).length == 0 || ! profiles.length">
                                <td colspan="7" class="text-center">No Records Found!</td>
                            </tr>                         

                        </tbody>
                    </table>            
                </div>
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(profiles | filter:search).length}} of @{{profiles.length}} entries</label> 
                </div>
        </div>
    </div>  

    <script src="/js/profile.js"></script>  
@stop