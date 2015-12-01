@extends('template')
@section('title')
Position
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/person"><h1>Position <i class="fa fa-briefcase"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="positionController">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_panel_title">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
                          <option>10</option>
                          <option>20</option>
                          <option>30</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        <a href="/position/create" class="btn btn-success">+ New Position</a>                        
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div style="padding-bottom: 10px">
                    <label for="search_name" class="search">Search Position:</label>
                    <input type="text" ng-model="search.job_title">
                </div>
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>                    
                        <th class="col-md-3 text-center">
                            <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                            Job Title
                            <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>                            
                        </th>
                        <th class="col-md-6 text-center">
                            Duty and Responsibility
                        </th>                     
                         <th class="col-md-2 text-center">
                            Action
                        </th>                                                                       
                    </tr>

                    <tbody>
                        <tr dir-paginate="position in positions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                            <td class="col-md-1">@{{ number }} </td>
                            <td class="col-md-3">@{{ position.job_title }}</td>
                            <td class="col-md-6">@{{ position.duty }}</td>
                            <td class="col-md-2">
                            <a href="/position/@{{ position.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(position.id)">Delete</button>
                            </td>
                        </tr>
                        <tr ng-show="(positions | filter:search).length == 0 || ! positions.length">
                            <td colspan="7">No Records Found!</td>
                        </tr>                         

                    </tbody>
                </table>            
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(positions | filter:search).length}} of @{{positions.length}} entries</label> 
                </div>
        </div>
    </div>  

    <script src="/js/position.js"></script>  
@stop