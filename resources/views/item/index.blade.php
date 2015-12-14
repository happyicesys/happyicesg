@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/item"><h1> {{ $ITEM_TITLE }}<i class="fa fa-shopping-cart"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="itemController">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_panel_title">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='30'">
                          <option>10</option>
                          <option>20</option>
                          <option>30</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    <div class="pull-right">
                        <a href="/item/create" class="btn btn-success">+ New {{ $ITEM_TITLE }}</a>                        
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div style="padding-bottom: 10px">
                    <label for="search_name" class="search">Search ID:</label>
                    <input type="text" ng-model="search.product_id">
                    <label for="search_company" class="search" style="padding-left: 10px">Product:</label>
                    <input type="text" ng-model="search.name">
                    <label for="search_contact" class="search" style="padding-left: 10px">Desc:</label>
                    <input type="text" ng-model="search.remark">
                </div>
                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>                    
                            <th class="col-md-1 text-center">
                                <a href="#" ng-click="sortType = 'product_id'; sortReverse = !sortReverse">
                                ID
                                <span ng-show="sortType == 'product_id' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'product_id' && sortReverse" class="fa fa-caret-up"></span>
                                </a>                                                        
                            </th>
                            <th class="col-md-4 text-center">
                                <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                Product
                                <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                </a>                            
                            </th>
                            <th class="col-md-3 text-center">
                                Desc
                            </th>                                                
                             <th class="col-md-1 text-center">
                                Unit
                            </th>
                             <th class="col-md-2 text-center">
                                Action
                            </th>                                                                                                
                        </tr>

                        <tbody>
                            <tr dir-paginate="item in items | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                <td class="col-md-1 text-center">@{{ number }} </td>
                                <td class="col-md-1 text-center">@{{ item.product_id }}</td>
                                <td class="col-md-4">@{{ item.name }}</td>
                                <td class="col-md-3">@{{ item.remark }}</td>
                                <td class="col-md-1 text-center">@{{ item.unit }}</td>
                                <td class="col-md-2 text-center">
                                    <a href="/item/@{{ item.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(item.id)">Delete</button>
                                </td>
                            </tr>
                            <tr ng-show="(items | filter:search).length == 0 || ! items.length">
                                <td colspan="7">No Records Found!</td>
                            </tr>                         

                        </tbody>
                    </table>            
                </div>
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(items | filter:search).length}} of @{{items.length}} entries</label> 
                </div>
        </div>
    </div>  

    <script src="/js/item.js"></script>  
@stop