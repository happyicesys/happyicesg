@inject('items', 'App\Item')
@inject('people', 'App\Person')
@inject('priceTemplates', 'App\PriceTemplate')
@extends('template')

@section('title')
Price Template
@stop
@section('content')

<div class="row">
    <a class="title_hyper pull-left" href="/price-template"><h1>Price Template <i class="fa fa-usd"></i></h1></a>
</div>
<div ng-app="app" ng-controller="priceTemplateController">
    <div class="row" style="padding-top: 10px;">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pull-right">
                <button class="btn btn-success" data-toggle="modal" data-target="#price-template-modal" ng-click="onPriceTemplateCreateClicked()">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Create Price Template
                </button>
            </div>
        </div>

    <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 5px;">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">
                    Create Binding
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="price_template_name" class="control-label">
                        Price Template Name
                    </label>
                    <select name="price_template_name" class="form-control select" ng-model="form.price_template_id">
                        <option value=""></option>
                        @foreach($priceTemplates->orderBy('name', 'asc')->get() as $priceTemplate)
                            <option value="{{$priceTemplate->id}}">
                                {{$priceTemplate->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="person_id" class="control-label">
                        Customer Name
                    </label>
                    <select name="person_id" class="form-control select" ng-model="form.person_id">
                        <option value=""></option>
                        @foreach($people->orderBy('cust_id', 'asc')->get() as $person)
                            <option value="{{$person->id}}">
                                {{$person->cust_id}} - {{$person->company}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-warning btn-md" ng-click="onPriceTemplateBindingClicked()">
                    <i class="fa fa-retweet" aria-hidden="true"></i>
                    Binding
                </button>
            </div>
        </div>
    </div>
    </div>

    <div class="panel panel-default">

        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name', 'ng-change' => "searchDB()"]) !!}
                </div>
                <div class="form-group col-md-4 col-sm-6 col-xs-12">
                    {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                    {!! Form::select('person_id', [''=>'All']+$people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id', 'asc')->pluck('full', 'id')->all(),
                        null,
                        [
                            'class'=>'selectmultiple form-control',
                            'ng-model'=>'search.person_id',
                            'multiple'=>'multiple',
                            'ng-change' => "searchDB()"
                        ])
                    !!}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4 col-xs-12">
                    @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                        {{-- <button class="btn btn-sm btn-primary" ng-click="exportCustCatGroupExcel($event)">Export Excel</button> --}}
                    @endif
                </div>
                <div class="col-md-offset-8 col-md-4 col-xs-12 text-right">
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

            <div class="row"></div>
            <div class="table-responsive" id="exportable_price_template">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                            Name
                            <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                            </a>
                        </th>
                        <th class="col-md-6 text-center">
                            Customer(s)
                        </th>
                    </tr>

                    <tbody>
                        <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage" pagination-id="priceTemplates" total-items="totalCount" current-page="currentPage">
                            <td class="col-md-1 text-center">
                                @{{ $index + indexFrom }}
                            </td>
                            <td class="col-md-2 text-center">
                                @{{ data.name }} <br>
                                <button class="btn btn-danger btn-xs" ng-click="onPriceTemplateDelete(data)">
                                    Delete
                                </button>
                            </td>
                            <td class="col-md-9 text-left">
                                <ul ng-repeat="person in data.people">
                                    <li>
                                        @{{person.cust_id}} - @{{person.name}} &nbsp;

                                        <button class="btn btn-warning btn-xs" ng-click="onPriceTemplateUnbind(person.id)">
                                            Unbind
                                        </button>
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
        </div>

        <div>
            <dir-pagination-controls max-size="5" pagination-id="priceTemplates" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>

    <div id="price-template-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                      @{{form.id ? 'Edit Price Template' : 'New Price Template'}}
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
                    <textarea class="form-control" ng-model="form.remarks" rows="3"></textarea>
                  </div>
                  <hr class="row">
                      <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <div class="form-group">
                                    <label for="item">
                                        Item
                                    </label>
                                    <select class="select form-control" ng-model="form.item">
                                        <option value=""></option>
                                        @foreach($items::with(['itemCategory', 'itemGroup'])->whereIsActive(1)->orderBy('product_id')->get() as $item)
                                        <option value="{{$item}}">
                                            {{$item->product_id}} - {{$item->name}} {{$item->remark}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="sequence">
                                        Sequence
                                    </label>
                                    <input type="text" class="form-control" ng-model="form.sequence">
                                </div>
                            </div>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <label for="retail_price">
                                    Retail Price
                                </label>
                                <input type="text" class="form-control" ng-model="form.retail_price">
                              </div>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <label for="quote_price">
                                    Quote Price
                                </label>
                                <input type="text" class="form-control" ng-model="form.quote_price">
                              </div>
                          </div>
                      </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="btn-group pull-left" style="padding-top: 10px;">
                                    <button type="button" class="btn btn-success" ng-click="onAddPriceTemplateItemClicked()" ng-disabled="!form.item">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    Add Pricing
                                    </button>
                                </div>
                            </div>
                        </div>

                  <div class="form-group" style="padding-top: 20px;">
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                        <tr style="background-color: #DDFDF8">
                          <th class="col-md-1 text-center">
                            #
                          </th>
                          <th class="col-md-1 text-center">
                            ID
                          </th>
                          <th class="col-md-3 text-center">
                            Product
                          </th>
                          <th class="col-md-2 text-center">
                            Desc
                          </th>
                          <th class="col-md-2 text-center">
                            Retail Price
                          </th>
                          <th class="col-md-2 text-center">
                            Quote Price
                          </th>
                          <th class="col-md-1 text-center">
                            Action
                          </th>
                        </tr>
                        <tr ng-repeat="priceTemplateItem in form.price_template_items | orderBy:'sequence'">
                          <td class="col-md-1 text-center">
                            <input type="text" class=" text-center" style="width:40px" ng-model="item.sequence" ng-value="priceTemplateItem.sequence = priceTemplateItem.sequence ? priceTemplateItem.sequence * 1 : '' " ng-model-options="{ debounce: 1000 }" ng-change="onFormSequenceChanged(priceTemplateItem, form.id)">
                          </td>
                          <td class="col-md-1 text-center">
                            <a href="/item/@{{ priceTemplateItem.item.id }}/edit">
                            @{{ priceTemplateItem.item.product_id }}
                            </a>
                          </td>
                          <td class="col-md-3 text-left">
                            @{{ priceTemplateItem.item.name }}
                          </td>
                          <td class="col-md-2 text-left">
                            @{{ priceTemplateItem.item.remark }}
                          </td>
                          <td class="col-md-2 text-right">
                            @{{ priceTemplateItem.retail_price }}
                          </td>
                          <td class="col-md-2 text-right">
                            @{{ priceTemplateItem.quote_price }}
                          </td>
                          <td class="col-md-1 text-center">
                            <button class="btn btn-danger btn-sm" ng-click="onSingleEntryDeleted(priceTemplateItem)">
                              <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                          </td>
                        </tr>
                        <tr ng-if="!form.price_template_items || form.price_template_items.length == 0">
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
<script src="/js/price-template.js"></script>
@stop