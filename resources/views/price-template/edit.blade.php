@inject('items', 'App\Item')
@inject('uoms', 'App\Uom')

@extends('template')
@section('title')
Price Template
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="priceTemplateController">
{!! Form::model($priceTemplate,['method'=>'POST','action'=>['PriceTemplateController@storeUpdatePriceTemplateApi', $priceTemplate->id], 'files'=>true]) !!}
{!! Form::hidden('priceTemplateId', $priceTemplate->id, ['id'=>'priceTemplateId']) !!}
    {{-- <form ng-submit="onFormSubmitClicked"> --}}
      {{-- <div id="price-template-modal" class="modal fade" role="dialog"> --}}
          <div class="panel panel-primary">
              {{-- <div class="modal-content"> --}}
                  <div class="panel-heading">
                      <h4 class="modal-title">
                        {{$priceTemplate->id ? 'Edit' : 'New'}} Price Template
                      </h4>
                  </div>
                  <div class="panel-body">
                    <div class="form-group">
                      <label for="template-name">
                        Template Name
                      </label>
                      <label style="color: red;">*</label>
                      {!! Form::text('name', null, ['class'=>'form-control input-sm', 'placeholder'=>'Name']) !!}
                    </div>
                    <div class="form-group">
                      <label for="template-name">
                        Template Desc
                      </label>
                      {!! Form::textarea('remarks', null, ['class'=>'form-control', 'rows'=>'3']) !!}
                    </div>

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
                                  @foreach($priceTemplate->attachments as $attachmentIndex => $attachment)
                                  <tr>
                                      <td class="col-md-1 text-center">
                                          {{$attachmentIndex + 1}}
                                      </td>
                                      <td class="col-md-9">
                                          <img src="{{$attachment->full_url}}" alt="{{$attachment->url}}" style="max-width:300px;">
                                      </td>
                                      <td class="col-md-2 text-center">
                                        <div class="btn-group">
                                            <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="removeFile({{$attachment->id}})"><i class="fa fa-trash"></i> <span class="hidden-xs">Delete</span></a>
                                            <a href="{{$attachment->full_url}}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-download"></i> <span class="hidden-xs">Open</span></a>
                                        </div>
                                      </td>
                                  </tr>
                                  @endforeach
                                  @if(!$priceTemplate->attachments()->exists())
                                  <tr>
                                      <td class="text-center" colspan="7">No Records Found</td>
                                  </tr>
                                  @endif
                              </tbody>
                          </table>
                        </div>

                      @if($priceTemplate->id)
                      <div class="form-group">
                          <div class="form-group">
                              <label for="files">Upload Image</label>
                              <input type="file" ng-files="setTheFiles($files)" id="image_file" class="form-control">
                          </div>
                          <div class="form-group">
                              <button class="btn btn-success" ng-click="uploadFile($event, {{$priceTemplate->id}})"><i class="fa fa-upload"></i> Upload File</button>
                              {{-- <button class="btn btn-danger" ng-click="deleteFile(form.id)"><i class="fa fa-times"></i> Remove File(s)</button> --}}
                          </div>
                      </div>
                      @endif


                    <hr class="row">
                      <div class="panel panel-primary">
                          <div class="panel-heading">
                              Create new Pricing
                          </div>
                          <div class="panel-body">
                              <div class="row">
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                      <div class="col-md-8 col-sm-8 col-xs-12">
                                          <div class="form-group">
                                              <label for="item">
                                                  Item
                                              </label>
                                              <label style="color: red;">*</label>
                                              <select class="select form-control" name="item_id" ng-model="form.item_id">
                                                  <option value=""></option>
                                                  @foreach($items::with(['itemCategory', 'itemGroup', 'itemUoms', 'itemUoms.uom'])->whereIsActive(1)->orderBy('product_id')->get() as $item)
                                                  <option value="{{$item->id}}">
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
                                              <input type="number" name="sequence" ng-model="form.sequence" class="form-control">
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
                                          <input type="number" name="retail_price" ng-model="form.retail_price" class="form-control">
                                      </div>
                                      <div class="col-md-6 col-sm-6 col-xs-12">
                                          <label for="quote_price">
                                              Quote Price
                                          </label>
                                          <input type="number" name="quote_price" ng-model="form.quote_price" class="form-control">
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="panel-footer">
                              <button type="button" ng-click="onAddPriceTemplateItemClicked($event, {{$priceTemplate->id}})" ng-disabled="!form.item_id" class="btn btn-success">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              Add Pricing
                              </button>
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
                            <th class="col-md-2 text-center">
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
                            @foreach($uoms::orderBy('sequence', 'desc')->get() as $uom)
                            <th class="col-md-1 text-center">
                              {{$uom->name}}
                            </th>
                            @endforeach
                            <th class="col-md-1 text-center">
                              Action
                            </th>
                          </tr>
                          @foreach($priceTemplate->priceTemplateItems as $priceTemplateItem)
                          <tr>
                            <td class="col-md-1 text-center">
                              <input type="text" name="sequence[{{$priceTemplateItem->id}}]" value="{{$priceTemplateItem->sequence}}" class=" text-center" style="width:40px">
                            </td>
                            <td class="col-md-1 text-center">
                              <a href="/item/{{ $priceTemplateItem->item->id }}/edit">
                                {{$priceTemplateItem->item->product_id}}
                              </a>
                            </td>
                            <td class="col-md-2 text-left">
                              {{$priceTemplateItem->item->name}}
                            </td>
                            <td class="col-md-2 text-left">
                              {{$priceTemplateItem->item->remark}}
                            </td>
                            <td class="col-md-2 text-right">
                              <input type="text" name="retail_price[{{$priceTemplateItem->id}}]" value="{{$priceTemplateItem->retail_price}}" class="form-control text-center">
                            </td>
                            <td class="col-md-2 text-right">
                              <input type="text" name="quote_price[{{$priceTemplateItem->id}}]" value="{{$priceTemplateItem->quote_price}}" class="form-control text-center">
                            </td>

                            @foreach($uoms::orderBy('sequence', 'desc')->get() as $uom)
                            <td class="col-md-1 text-center">
                              @if(!$priceTemplateItem->item->is_inventory and $uom->name == 'ctn')
                              <span>
                                  <input type="checkbox" checked disabled>
                              </span>
                              @elseif($priceTemplateItem->item->is_inventory)
                                @foreach($priceTemplateItem->item->itemUoms as $itemUom)
                                  @if($itemUom->uom_id == $uom->id)
                                  <span>
                                      <input type="checkbox" name="priceTemplateUom[{{$priceTemplateItem->id}}][{{$itemUom->id}}]" {{$priceTemplateItem->priceTemplateItemUoms()->where('price_template_item_id', $priceTemplateItem->id)->where('item_uom_id', $itemUom->id)->first() ? 'checked' : ''}}>
                                  </span>
                                  @endif
                                @endforeach
                              @endif
                            </td>
                            @endforeach
                            <td class="col-md-1 text-center">
                              <button class="btn btn-danger btn-sm" ng-click="onSingleEntryDeleted({{$priceTemplateItem->id}})">
                                <i class="fa fa-times" aria-hidden="true"></i>
                              </button>
                            </td>
                          </tr>
                          @endforeach
                          @if(!$priceTemplate->priceTemplateItems()->exists())
                          <tr>
                            <td colspan="14" class="text-center">No Records Found</td>
                          </tr>
                          @endif
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                      <div class="btn-group">
                          <button type="button" name="replicate" ng-click="onReplicatePriceTemplateClicked($event, {{$priceTemplate->id}})" class="btn btn-info">
                              Replicate
                          </button>
                          <button type="submit" class="btn btn-success">
                            @if($priceTemplate->id)
                              Save
                            @else
                              Submit
                            @endif
                          </button>
                          <button type="button" ng-click="onBackButtonClicked($event)" class="btn btn-default">Back</button>
                      </div>
                  </div>
          </div>
      {!! Form::close() !!}
</div>
<script src="/js/price-template-edit.js"></script>
@stop