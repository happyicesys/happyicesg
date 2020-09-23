@inject('custcategories', 'App\Custcategory')
@inject('profiles', 'App\Profile')
@inject('franchisees', 'App\User')
@inject('people', 'App\Person')
@inject('persontags', 'App\Persontag')
@inject('users', 'App\User')
@inject('zones', 'App\Zone')

@extends('template')

@section('title')
Route Template
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/route-template"><h1>Route Template <i class="fa fa-road"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="routeTemplateController">

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="pull-right">
                      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#route-template-modal" ng-click="onAddRouteTemplateButtonClicked()">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Add Route Template
                      </button>
                    </div>
                </div>
            </div>

            <div class="panel-body">
              <div class="row">
                  <div class="form-group col-md-2 col-sm-4 col-xs-12">
                      {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                      <label class="pull-right">
                          <input type="checkbox" name="strictCustId" ng-model="search.strictCustId" ng-change="searchDB()">
                          <span style="margin-top: 5px; margin-right: 5px;">
                              Strict
                          </span>
                      </label>
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
                  <div class="form-group col-md-2 col-sm-4 col-xs-12">
                      {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                      <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                          <option value="">All</option>
                          @foreach($custcategories::orderBy('name')->get() as $custcategory)
                          <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="form-group col-md-2 col-sm-4 col-xs-12">
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

                  <div class="form-group col-md-2 col-sm-4 col-xs-12">
                      {!! Form::label('tags', 'Tags', ['class'=>'control-label search-title']) !!}
                      <select name="tags" id="tags" class="selectmultiple form-control" ng-model="search.tags" ng-change="searchDB()" multiple>
                          <option value="">All</option>
                          @foreach($persontags::orderBy('name')->get() as $persontag)
                              <option value="{{$persontag->id}}">
                                  {{$persontag->name}}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  <div class="form-group col-md-2 col-sm-4 col-xs-12">
                    {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('profile_id', [''=>'All']+$profiles::filterUserProfile()->pluck('name', 'id')->all(), null, ['id'=>'profile_id',
                        'class'=>'select form-control',
                        'ng-model'=>'search.profile_id',
                        'ng-change' => 'searchDB()'
                        ])
                    !!}
                  </div>
                  <div class="form-group col-md-2 col-sm-4 col-xs-12">
                      {!! Form::label('zone_id', 'Zone', ['class'=>'control-label']) !!}
                      {!! Form::select('zone_id',
                              [''=>'All']+ $zones::orderBy('priority')->lists('name', 'id')->all(),
                              null,
                              [
                                  'class'=>'select form-control',
                                  'ng-model'=>'search.zone_id',
                                  'ng-change'=>'searchDB()'
                              ])
                      !!}
                  </div>
              </div>

                    <div class="row" style="padding-top: 20px;">
                        <div class="col-md-4 col-xs-12">
                            <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
                        </div>
                        <div class="col-md-4 col-md-offset-4 col-xs-12 text-right">
                            <div class="row" style="padding-right:18px;">
                                <label>Display</label>
                                <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='All'" ng-change="pageNumChanged()">
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
                <hr>
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                      <label>Invoice Date</label>
                      <datepicker>
                          <input
                              type = "text"
                              class = "form-control input-sm"
                              placeholder = "Invoice Date"
                              ng-model = "search.invoice_date"
                              ng-change = "onDateChange('invoice_date', search.invoice_date)"
                          />
                      </datepicker>
                    </div>

                    <button class="btn btn-success" ng-click="onGenerateClicked()" ng-disabled="!search.invoice_date" style="margin-top:20px;">
                      Generate
                    </button>
                  </div>
                </div>

                <div class="table-responsive" id="exportable" style="padding-top: 20px;">
                    <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                <input type="checkbox" id="check_all" ng-model="checkall" ng-change="onCheckAllChecked()"/>
                            </th>
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('name')">
                                Name
                                <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('desc')">
                                Desc
                                <span ng-if="search.sortName == 'desc' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'desc' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-5 text-center">
                                Route(s)
                            </th>
                        </tr>

                        <tbody>
                            <tr dir-paginate="routeTemplate in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">

                                <td class="col-md-1 text-center">
                                    <input type="checkbox" name="checkbox" ng-model="routeTemplate.check">
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{ $index + indexFrom }}
                                </td>
                                <td class="col-md-2">
                                    <a href="#" ng-click="onSingleRouteTemplateClicked(routeTemplate)" data-toggle="modal" data-target="#route-template-modal">
                                    @{{ routeTemplate.name }}
                                    </a>
                                </td>
                                <td class="col-md-2">
                                    @{{ routeTemplate.desc }}
                                </td>
                                <td class="col-md-5 text-left">
                                  <ul ng-repeat="item in routeTemplate.route_template_items | orderBy:'sequence'">
                                    <li>
                                      [@{{item.sequence}}] @{{item.person.cust_id}} - @{{item.person.company}} (@{{item.person.del_postcode}})
                                    </li>
                                  </ul>
                                </td>
                            </tr>
                            <tr ng-if="!alldata || alldata.length == 0">
                                <td colspan="14" class="text-center">No Records Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
                </div>
        </div>

      <div id="route-template-modal" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">
                        @{{form.id ? 'Edit Route Template' : 'New Route Template'}}
                        <span ng-if="form.id">
                          @{{form.name}}
                        </span>
                      </h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="template-name">
                        Template Name
                      </label>
                      <label style="color: red;">*</label>
                      <input type="text" class="form-control" ng-model="form.name">
                    </div>
                    <div class="form-group">
                      <label for="template-name">
                        Template Desc
                      </label>
                      <textarea class="form-control" ng-model="form.desc" rows="3"></textarea>
                    </div>
                    <hr class="row">
                      <div class="form-group">
                        <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="col-md-8 col-sm-8 col-xs-12">
                            <label for="customer">
                              Customer
                            </label>
                            <select class="select form-control" ng-model="form.person">
                              <option value=""></option>
                              @foreach($people::with(['custcategory', 'zone'])->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->orderBy('cust_id')->get() as $person)
                                <option value="{{$person}}">
                                  {{$person->cust_id}} - {{$person->company}}
                                </option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-12">
                            <label for="sequence">
                              Sequence
                            </label>
                            <input type="text" class="form-control" ng-model="form.sequence">
                          </div>
                          <div class="btn-group pull-left" style="padding-top: 10px;">
                            <button type="button" class="btn btn-success" ng-click="onAddRouteClicked()" ng-disabled="!form.person">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              Add Route
                            </button>
                          </div>
                        </div>
                        </div>

                      </div>
                    <div class="form-group">
                      <label for="template-name">
                        Route(s)
                      </label>
                      <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                          <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                              #
                            </th>
                            <th class="col-md-1 text-center">
                              ID
                            </th>
                            <th class="col-md-2 text-center">
                              ID Name
                            </th>
                            <th class="col-md-1 text-center">
                              Cat
                            </th>
                            <th class="col-md-1 text-center">
                              Postcode
                            </th>
                            <th class="col-md-4 text-center">
                              Address
                            </th>
                            <th class="col-md-1 text-center">
                              Zone
                            </th>
                            <th class="col-md-1 text-center">
                              Action
                            </th>
                          </tr>
                          <tr ng-repeat="item in form.route_template_items | orderBy:'sequence'">
                            <td class="col-md-1 text-center">
                              <input type="text" class=" text-center" style="width:40px" ng-model="item.sequence" ng-value="item.sequence = item.sequence ? item.sequence * 1 : '' " ng-model-options="{ debounce: 1000 }" ng-change="onFormSequenceChanged(item, form.id)">
                            </td>
                            <td class="col-md-1 text-center">
                              <a href="/person/@{{ item.person.id }}/edit">
                              @{{ item.person.cust_id }}
                              </a>
                            </td>
                            <td class="col-md-2 text-left">
                              @{{ item.person.company }}
                            </td>
                            <td class="col-md-1 text-center">
                              @{{ item.person.custcategory.name }}
                            </td>
                            <td class="col-md-1 text-center">
                              @{{ item.person.del_postcode }}
                            </td>
                            <td class="col-md-4 text-left">
                              @{{ item.person.del_address }}
                            </td>
                            <td class="col-md-4 text-left">
                              @{{ item.person.zone.name }}
                            </td>
                            <td class="col-md-1 text-center">
                              <button class="btn btn-danger btn-sm" ng-click="onSingleEntryDeleted(item)">
                                <i class="fa fa-times" aria-hidden="true"></i>
                              </button>
                            </td>
                          </tr>
                          <tr ng-if="!form.route_template_items || form.route_template_items.length == 0">
                            <td colspan="14" class="text-center">No Records Found</td>
                        </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="!form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.name">Submit</button>
                      <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.name">Save</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
              </div>

          </div>
      </div>

    </div>

    <script src="/js/route-template.js"></script>
@stop