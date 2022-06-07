@extends('template')
@section('title')
{{ $ITEM_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="itemController">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h3 class="panel-title"><strong>Editing {{$item->product_id}} : {{$item->name}} </strong></h3>
        </div>

        <div class="panel-body">
            {!! Form::model($item,['method'=>'PATCH','action'=>['ItemController@update', $item->id], 'files'=>true]) !!}
            {!! Form::hidden('item_id', $item->id, ['id'=>'item_id']) !!}
            {!! Form::hidden('image_remain', $item->img_remain, ['id'=>'img_remain']) !!}

                @include('item.form')

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="pull-right" >
                        {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
            {!! Form::close() !!}

                        <a href="/item" class="btn btn-default">Cancel</a>
                    </div>
                    <div class="pull-left">
                        @if($item->is_active == '0')
                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_form']) !!}
                            {!! Form::submit('Activate', ['class'=> 'btn btn-success', 'form'=>'active_form']) !!}
                        @else
                            {!! Form::submit('Deactivate', ['class'=> 'btn btn-warning', 'form'=>'active_form']) !!}
                        @endif
                    </div>
                </div>
        </div>
    </div>

    {!! Form::open(['id'=>'delete_form', 'method'=>'DELETE', 'action'=>['ItemController@destroy', $item->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
    {!! Form::close() !!}

    {!! Form::open(['id'=>'active_form', 'method'=>'POST', 'action'=>['ItemController@setActiveState', $item->id], 'onsubmit'=>'return confirm("Are you sure you want to activate/deactivate the item?")']) !!}
    {!! Form::close() !!}

    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <span class="pull-left">
                    <strong>
                        UOM: {{$item->product_id}} - {{$item->name}}
                    </strong>
                </span>
                <button class="btn btn-success pull-right" >
                    <i class="fa fa-plus"></i>
                    Create
                </button>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-3 text-center">
                            UOM
                        </th>
                        <th class="col-md-3 text-center">
                            Value
                        </th>
                        <th class="col-md-3 text-center">
                            Label
                        </th>
                        <th class="col-md-2"></th>
                    </tr>
                    <tbody>
                        <tr ng-repeat="itemUom in itemUoms">
                            <td class="col-md-1 text-center">
                                @{{ $index + 1 }}
                            </td>
                            <td class="col-md-3 text-center">
                                @{{ itemUom.uom.name }}
                            </td>
                            <td class="col-md-3 text-right">
                                @{{ itemUom.value }}
                            </td>
                            <td class="col-md-3 text-center">
                                <span ng-if="itemUom.is_base_unit" class="badge badge-primary" style="background-color: #296192">
                                    Base UOM
                                </span>
                                <span ng-if="itemUom.is_transacted_unit" class="badge badge-success" style="background-color: #5ab55a;">
                                    Transacted UOM
                                </span>
                            </td>
                            <td class="col-md-2 text-center">
                                <div class="btn-group">
                                    <a href="#" class="btn btn-default btn-sm" ng-click="onItemUomEditClicked($event)">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm" ng-click="onItemUomDeleteClicked($event)">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr ng-if="!itemUoms || itemUoms.length == 0">
                            <td colspan="24" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- divider --}}
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <h3 class="panel-title"><strong>Photos : {{$item->name}}</strong></h3>
            </div>
        </div>

        <div class="panel-body">
        {!! Form::open(['id'=>'edit_caption', 'action'=>['ItemController@editCaption', $item->id]]) !!}
            <div class="table-responsive">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-4 text-center">
                        Photo
                    </th>
                    <th class="col-md-6 text-center">
                        Caption
                    </th>
                    <th class="col-md-1 text-center">
                        Action
                    </th>
                </tr>

                <tbody>
                    <tr ng-repeat="image in images" class="form-group">
                        <td class="col-md-1 text-center">
                            @{{ $index + 1 }}
                        </td>
                        <td class="col-md-4">
                            <a href="@{{image.path}}">
                                <img ng-src="@{{image.path}}" height="250" width="250">
                            </a>
                        </td>
                        <td class="col-md-6 text-center">
                            <input type="text" name="caption[@{{image.id}}]" class="form-control" ng-init="captionModel = getCaptionInit(image.id)" ng-model="captionModel"/>
                        </td>
                        <td class="col-md-1 text-center">
                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(image.id)">Delete</button>
                        </td>
                    </tr>

                    <tr ng-if="images.length == 0 || ! images.length">
                        <td colspan="4" class="text-center">No Records Found!</td>
                    </tr>
                </tbody>
            </table>
            </div>
            {!! Form::submit('Done', ['class'=> 'btn btn-success pull-right', 'form'=>'edit_caption']) !!}
        </div>
        {!! Form::close() !!}

        <div class="panel-footer">
            {!! Form::open(['action'=>['ItemController@addImage', $item->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
            {!! Form::close() !!}


            <label class="pull-right totalnum" for="totalnum">
                Total of @{{imageLength}}/ 4 images uploaded
            </label>
        </div>
    </div>
    {{-- divider --}}
</div>

<div id="uom-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                  @{{formUom.id ? 'Edit UOM' : 'New UOM'}}
                  <span ng-if="form.id">
                    @{{formUom.name}}
                  </span>
                </h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="template-name">
                  Name
                </label>
                <label style="color: red;">*</label>
                <input type="text" class="form-control" ng-model="formUom.name">
              </div>
              <div class="form-group">
                <label for="template-name">
                  Template Desc
                </label>
                <textarea class="form-control" ng-model="form.remarks" rows="3"></textarea>
              </div>
                <div ng-if="form.attachments && form.id">
                    <div class="table-responsive">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>
                                <th class="col-md-9 text-center">
                                    Path
                                </th>
                                <th class="col-md-2 text-center">
                                    Action
                                </th>
                            </tr>

                            <tbody>
                                <tr ng-repeat="attachment in form.attachments">
                                    <td class="col-md-1 text-center">
                                        @{{$index + 1}}
                                    </td>
                                    <td class="col-md-9">
                                        <img src="@{{attachment.url}}" alt="@{{attachment.url}}" style="max-width:300px;">
                                    </td>
                                    <td class="col-md-2 text-center">
                                        <div class="btn-group">
                                            <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="removeFile(attachment.id)"><i class="fa fa-trash"></i> <span class="hidden-xs">Delete</span></a>
                                            <a href="@{{attachment.url}}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> <span class="hidden-xs">Open</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr ng-if="form.attachments.length == 0">
                                    <td class="text-center" colspan="7">No Records Found</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                </div>
                <div ng-if="form.id" class="form-group">
                    <div class="form-group">
                        <label for="files">Upload Image</label>
                        <input type="file" ng-files="setTheFiles($files)" id="image_file" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success" ng-click="uploadFile($event, form.id)"><i class="fa fa-upload"></i> Upload File</button>
                        {{-- <button class="btn btn-danger" ng-click="deleteFile(form.id)"><i class="fa fa-times"></i> Remove File(s)</button> --}}
                    </div>
                </div>

              <hr class="row">
                  <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="form-group">
                                <label for="item">
                                    Item
                                </label>
                                <label style="color: red;">*</label>
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
                                <input type="number" class="form-control" ng-model="form.sequence">
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
                            <input type="number" class="form-control" ng-model="form.retail_price">
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <label for="quote_price">
                                Quote Price
                            </label>
                            <input type="number" class="form-control" ng-model="form.quote_price">
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
                        <th colspan="7">
                            <button type="button" class="btn btn-xs btn-warning" ng-click="onSortSequenceClicked($event)">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                                Sort
                            </button>
                            <button class="btn btn-xs btn-default" ng-click="onRenumberSequenceClicked($event)">
                                Re-number
                            </button>
                        </th>
                    </tr>
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
                    <tr ng-repeat="priceTemplateItem in form.price_template_items">
                      <td class="col-md-1 text-center">
                        <input type="text" class=" text-center" style="width:40px" ng-model="priceTemplateItem.sequence" ng-value="priceTemplateItem.sequence = priceTemplateItem.sequence ? priceTemplateItem.sequence * 1 : '' " ng-model-options="{ debounce: 1000 }">
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
                        <input type="text" class="form-control text-center" ng-model="priceTemplateItem.retail_price" ng-model-options="{ debounce: 1000 }">
                      </td>
                      <td class="col-md-2 text-right">
                        <input type="text" class="form-control text-center" ng-model="priceTemplateItem.quote_price" ng-model-options="{ debounce: 1000 }">
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
                <div class="btn-group">
                    <button class="btn btn-info" ng-click="onReplicatePriceTemplateClicked(form)">
                        Replicate
                    </button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="!form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.name">Submit</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.name">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="/js/item_edit.js"></script>

@stop