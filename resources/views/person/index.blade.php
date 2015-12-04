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
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
                          <option>10</option>
                          <option>20</option>
                          <option>30</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        <a href="/person/create" class="btn btn-success">+ New {{ $PERSON_TITLE }}</a>                        
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div style="padding-bottom: 10px">
                    <label for="search_name" class="search">Search ID:</label>
                    <input type="text" ng-model="search.cust_id">
                    <label for="search_company" class="search" style="padding-left: 10px">Company:</label>
                    <input type="text" ng-model="search.company">
                    <label for="search_contact" class="search" style="padding-left: 10px">Contact:</label>
                    <input type="text" ng-model="search.contact">
                </div>
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>                    
                        <th class="col-md-1 text-center">
                            ID                           
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'company'; sortReverse = !sortReverse">
                            Company
                            <span ng-show="sortType == 'company' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'company' && sortReverse" class="fa fa-caret-up"></span>
                            </a>                            
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                            Att. To
                            <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-2 text-center">
                            Contact
                        </th>                                                
                         <th class="col-md-2 text-center">
                            Delivery Add
                        </th>
                         <th class="col-md-2 text-center">
                            Action
                        </th>                                                                                                
                    </tr>

                    <tbody>
                        <tr dir-paginate="person in people | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                            <td class="col-md-1 text-center">@{{ number }} </td>
                            <td class="col-md-1">@{{ person.cust_id }}</td>
                            <td class="col-md-2">@{{ person.company }}</td>
                            <td class="col-md-2">@{{ person.name }}</td>
                            <td class="col-md-2">
                                @{{ person.contact }}
                                <span ng-show="person.alt_contact.length > 0">
                                / @{{ person.alt_contact }}
                                </span>
                            </td>
                            <td class="col-md-2">@{{ person.del_address }}</td>
                            <td class="col-md-2 text-center">
                                <a href="/person/@{{ person.id }}/edit" class="btn btn-sm btn-primary">Profile</a>
                                <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(person.id)">Delete</button>
                            </td>
                        </tr>
                        <tr ng-show="(people | filter:search).length == 0 || ! people.length">
                            <td colspan="7">No Records Found!</td>
                        </tr>                         

                    </tbody>
                </table>            
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(people | filter:search).length}} of @{{people.length}} entries</label> 
                </div>
        </div>
    </div>  

    <script src="/js/person.js"></script>  
@stop