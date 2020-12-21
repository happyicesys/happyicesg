@inject('custcategories', 'App\Custcategory')
@inject('profiles', 'App\Profile')
@inject('users', 'App\User')

@extends('template')

@section('title')
Potential Customer
@stop
@section('content')

    <div class="row">
        <a class="title_hyper pull-left" href="/potential-customer"><h1>Potential Customer <i class="fa fa-address-card-o"></i></h1></a>
    </div>
    <div ng-app="app" ng-controller="potentialCustomerController">

        <div class="panel panel-default" ng-cloak>
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="pull-right">
                      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#potential-customer-modal" ng-click="onAddPotentialCustomerTemplateButtonClicked()">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Potential Customer
                      </button>
                    </div>
                </div>
            </div>

            <div class="panel-body">
              <div class="row">
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
                      {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                      {!! Form::text('name', null,
                                                      [
                                                          'class'=>'form-control input-sm',
                                                          'ng-model'=>'search.name',
                                                          'placeholder'=>'Name',
                                                          'ng-change'=>'searchDB()',
                                                          'ng-model-options'=>'{ debounce: 500 }'
                                                      ])
                      !!}
                  </div>
                    <div class="form-group col-md-2 col-sm-4 col-xs-12">
                        {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                        @if(auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                            <select name="account_manager" class="select form-control" ng-model="search.account_manager" ng-change="searchDB()" ng-init="merchandiserInit('{{auth()->user()->id}}')" disabled>
                                <option value="">All</option>
                                @foreach($users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                                <option value="{{$user->id}}">
                                    {{$user->name}}
                                </option>
                                @endforeach
                            </select>
                        @else
                            {!! Form::select('account_manager',
                                    [''=>'All']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                                    null,
                                    [
                                        'class'=>'select form-control',
                                        'ng-model'=>'search.account_manager',
                                        'ng-change'=>'searchDB()'
                                    ])
                            !!}
                        @endif
                    </div>  
                    <div class="form-group col-md-2 col-sm-4 col-xs-12">
                        {!! Form::label('contact', 'Contact', ['class'=>'control-label search-title']) !!}
                        {!! Form::text('contact', null,
                                                        [
                                                            'class'=>'form-control input-sm',
                                                            'ng-model'=>'search.contact',
                                                            'placeholder'=>'Contact',
                                                            'ng-change'=>'searchDB()',
                                                            'ng-model-options'=>'{ debounce: 500 }'
                                                        ])
                        !!}
                    </div>                                    
              </div>

                    <div class="row" style="padding-top: 20px;">
                        <div class="col-md-4 col-xs-12">
                            {{-- <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button> --}}
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

                <div class="table-responsive" id="exportable" style="padding-top: 20px;">
                    <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                        <tr style="background-color: #DDFDF8">
                            <th class="col-md-1 text-center">
                                #
                            </th>
                            <th class="col-md-2 text-center">
                                <a href="" ng-click="sortTable('name')">
                                Name
                                <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('custcategory_name')">
                                Cust Category
                                <span ng-if="search.sortName == 'custcategory_name' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'custcategory_name' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                <a href="" ng-click="sortTable('account_manager')">
                                Acc Manager
                                <span ng-if="search.sortName == 'account_manager' && !search.sortBy" class="fa fa-caret-down"></span>
                                <span ng-if="search.sortName == 'account_manager' && search.sortBy" class="fa fa-caret-up"></span>
                            </th>
                            <th class="col-md-1 text-center">
                                Attn To
                            </th>  
                            <th class="col-md-1 text-center">
                                Contact
                            </th> 
                            <th class="col-md-1 text-center">
                                Address
                            </th> 
                            <th class="col-md-1 text-center">
                                Postcode
                            </th> 
                            <th class="col-md-1 text-center">
                                Remarks
                            </th>     
                            <th class="col-md-1 text-center">
                                Created By
                            </th>
                            <th class="col-md-1 text-center">
                                Updated By
                            </th>                                                                                                                                                                                                                               
                            <th class="col-md-1 text-center"></th>
                        </tr>

                        <tbody>
                            <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                                <td class="col-md-1 text-center">
                                    @{{ $index + indexFrom }}
                                </td>
                                <td class="col-md-2 text-center">
                                    @{{data.name}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{data.custcategory.name}}
                                </td>  
                                <td class="col-md-1 text-center">
                                    @{{data.account_manager.name}}
                                </td>   
                                <td class="col-md-1 text-center">
                                    @{{data.attn_to}}
                                </td>  
                                <td class="col-md-1 text-center">
                                    @{{data.contact}}
                                </td>    
                                <td class="col-md-1 text-center">
                                    @{{data.address}}
                                </td>
                                <td class="col-md-1 text-center">
                                    @{{data.postcode}}
                                </td>    
                                <td class="col-md-2 text-center">
                                    @{{data.remarks}}
                                </td>  
                                <td class="col-md-1 text-center">
                                    @{{data.creator.name}} <br>
                                    @{{data.created_at}}
                                </td>   
                                <td class="col-md-1 text-center">
                                    @{{data.updater.name}} <br>
                                    @{{data.updated_at}}
                                </td>                                                                                                                                                                                                                                                                                            
                                <td class="col-md-1 text-center">
                                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#potential-customer-modal" ng-click="onSingleEntryEdit(data)">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </button>        
{{--                                                                 
                                    <button class="btn btn-danger btn-sm" ng-click="onSingleEntryRemove(data.id)">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button> --}}
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

      <div id="potential-customer-modal" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">
                        @{{form.id ? 'Edit Potential Customer' : 'New Potential Customer'}}
                        <span ng-if="form.id">
                          @{{form.name}}
                        </span>
                      </h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                        <label for="name">
                            Name
                        </label>
                        <label style="color: red;">*</label>
                        <input type="text" class="form-control" ng-model="form.name">
                    </div>
                    <div class="form-group">
                        <label for="custcategory">
                            Custcategory
                        </label>
                        <select class="select form-control" ng-model="form.custcategory_id">
                            <option value=""></option>
                            @foreach($custcategories::orderBy('name')->get() as $custcategory)
                            <option value="{{$custcategory->id}}">
                                {{$custcategory->name}}
                            </option>
                            @endforeach
                        </select>                        
                    </div>   
                    <div class="form-group">
                        <label for="account_manager">
                            Account Manager
                        </label>
                        <select class="select form-control" ng-model="form.account_manager_id">
                            <option value=""></option>
                            @foreach($users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                            <option value="{{$user->id}}">
                                {{$user->name}}
                            </option>
                            @endforeach
                        </select>                        
                    </div>  
                    <div class="form-group">
                        <label for="attn_to">
                            Attn To
                        </label>
                        <input type="text" class="form-control" ng-model="form.attn_to">
                    </div>  
                    <div class="form-group">
                        <label for="contact">
                            Contact
                        </label>
                        <input type="text" class="form-control" ng-model="form.contact">
                    </div>    
                    <div class="form-group">
                        <label for="address">
                            Address
                        </label>
                        <textarea class="form-control" rows="4" ng-model="form.address"></textarea>
                    </div>  
                    <div class="form-group">
                        <label for="postcode">
                            Postcode
                        </label>
                        <input type="text" class="form-control" ng-model="form.postcode">
                    </div>   
                    <div class="form-group">
                        <label for="remarks">
                            Remarks
                        </label>
                        <textarea class="form-control" rows="4" ng-model="form.remarks"></textarea>
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

    <script src="/js/potential-customer.js"></script>
@stop