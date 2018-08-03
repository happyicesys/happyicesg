@inject('profiles', 'App\Profile')
@inject('people', 'App\Person')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')

@extends('template')
@section('title')
SIM Card
@stop
@section('content')

    <div ng-app="app" ng-controller="simcardController">

    <div class="row">
        <a class="title_hyper pull-left" href="/simcard"><h1>SIM Card <i class="fa fa-phone-square"></i> <span ng-show="spinner"> <i class="fa fa-spinner fa-1x fa-spin"></i></span></h1></a>
    </div>

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pull-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#simcard_modal" ng-click="createSimcardModal()">
                                <i class="fa fa-plus"></i>
                                Add SIM Card
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('phone_no', 'Phone Num', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('phone_no', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.phone_no',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Phone Num',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ])
                        !!}
                    </div>                        
                    <div class="form-group col-md-3 col-sm-6 col-xs-12">
                        {!! Form::label('telco_name', 'Telco Name', ['class'=>'control-label search-title']) !!}
                        {!! Form::select('telco_name', [''=>'All', 'Singtel'=>'Singtel', 'Starhub'=>'Starhub', 'M1'=>'M1', 'Redone'=>'Redone'], null,
                            [
                            'class'=>'select form-control',
                            'ng-model'=>'search.telco_name',
                            'ng-change'=>'searchDB()'
                            ])
                        !!}
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-8 col-sm-6 col-xs-12">
                        <button class="btn btn-primary" ng-click="exportData($event)">Export Excel</button>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                        <div class="row">
                            <label for="display_num">Display</label>
                            <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                                <option ng-value="100">100</option>
                                <option ng-value="200">200</option>
                                <option ng-value="All">All</option>
                            </select>
                            <label for="display_num2" style="padding-right: 20px">per Page</label>
                        </div>
                        <div class="row">
                            <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" id="exportable" style="padding-top:20px;">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('phone_no')">
                                Phone Number
                                <span ng-if="search.sortName == 'phone_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'phone_no' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('telco_name')">
                                Telco Name
                                <span ng-if="search.sortName == 'telco_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'telco_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('simcard_no')">
                                SIM Card No
                                <span ng-if="search.sortName == 'simcard_no' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'simcard_no' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('updated_by')">
                                Last Modified By
                                <span ng-if="search.sortName == 'updated_by' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'updated_by' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('simcards.updated_at')">
                                Last Modified Time
                                <span ng-if="search.sortName == 'simcards.updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'simcards.updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Action
                            </th>
                        </tr>
                        <tbody>
                            <tr dir-paginate="simcard in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount">
                                <td class="col-md-1 text-center">@{{ $index + indexFrom }} </td>
                                <td class="col-md-2 text-center">
                                    <a href="#" data-toggle="modal" data-target="#simcard_modal" ng-click="editSimcardModal(simcard)">
                                        @{{ simcard.phone_no }}
                                    </a>
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{ simcard.telco_name }} 
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{ simcard.simcard_no }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ simcard.updater }}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ simcard.updated_at }}
                                </td>  
                                <td class="col-md-1 text-center">
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete($event, simcard.id)"><i class="fa fa-times"></i></button>
                                </td>                              
                            </tr>
                            <tr ng-if="!alldata || alldata.length == 0">
                                <td colspan="18" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                </div>
        </div>
    </div>

    <div class="modal fade" id="simcard_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">
                    @{{form.id ? 'Edit SIM Card' : 'Create SIM Card'}}
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Phone Num
                            </label>
                            <input type="text" name="phone_no" class="form-control" ng-model="form.phone_no">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Telco Name
                            </label>
                            <select name="telco_name" id="telco_name" class="select form-control" ng-model="form.telco_name">
                                <option value="Singtel">Singtel</option>
                                <option value="Starhub">Starhub</option>
                                <option value="M1">M1</option>
                                <option value="Redone">Redone</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                SIM Card Num
                            </label>
                            <input type="text" name="simcard_no" class="form-control" ng-model="form.simcard_no">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="createSimcard()" data-dismiss="modal" ng-if="!form.id">Create</button>
                    <button type="button" class="btn btn-success" ng-click="editSimcard()" data-dismiss="modal" ng-if="form.id">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>    

    <script src="/js/simcard_index.js"></script>
@stop