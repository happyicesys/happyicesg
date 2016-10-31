@inject('salesitems', 'App\D2dOnlineSale')

@extends('template_client')
@section('title')
Door To Door
@stop
@section('content')
<div id="d2dorderController">

<section id="portfolio" style="padding:25px 5px 10px 0px;">
  <div class="container-fluid">
    <div class="row no-gutter">
      <div class="col-md-5 col-xs-12">
        <a href="#" class="portfolio-box">
          <img src="img/d2d/a5 menu_final.jpg" class="img-responsive" alt="door to door ice cream delivery menu">
          <div class="portfolio-box-caption-content">
              <div class="project-category text-faded">
              </div>
          </div>
        </a>
      </div>
      <div class="col-md-7 col-xs-12">
        <order></order>
      </div>
    </div>
  </div>
</section>
</div>

<template id="order-template">
<div class="panel panel-primary">
  <div class="panel-body">
    <fieldset v-if="step1">
      <div class="form-top">
        <div class="form-top-left">
          <h3 style="color:#323299;">Step 1 / 3</h3>
          <p>Please enter your postcode</p>
        </div>
      </div>
        <div class="row">
          <div class="col-md-6 col-xs-12">
            <div class="form-bottom">
              <div class="form-group" :class="{ 'has-error' : formErrors['postcode'] }">
                <input type="text" name="postcode" placeholder="Postcode..." class="form-first-name form-control input-lg" v-model="form.postcode">
              </div>
              <span v-if="formErrors['postcode']" class="help-block" style="color:red;">
                <ul class="row">
                    <li style="color:red;">@{{ formErrors['postcode'][0] }}</li>
                </ul>
              </span>
              <button type="button" style="border-radius: 5px;" class="btn btn-success btn-next btn-lg" @click="verifyPostcode">Next <i class="fa fa-spinner fa-spin" v-if="loading"></i></button>
            </div>
          </div>
        </div>
    </fieldset>

    {!! Form::open(['action'=>'D2dOnlineSaleController@submitOrder']) !!}
    <div v-if="step2">
      <div class="col-md-12 col-xs-12">
          <h3 style="color:#323299;">Step 2 / 3</h3>
          <div class="table-responsive">
          <table class="table table-list-search table-hover table-bordered add_item" style="margin-top:10px;">
          <tr style="background-color: #f7f9f7">
            <th class="col-md-1 text-center">
              #
            </th>
            <th class="col-md-6 text-center">
              Item
            </th>
            <th class="col-md-2 text-center">
              Qty
            </th>
            <th class="col-md-2 text-center">
              Price
            </th>
          </tr>
          <tr is="sales-item"
              v-for="(item, number) in items"
              :number="number + 1"
              :item="item"
              :items="items"
              :subtotal="subtotal"
              @beforeamount="deductTotal"
              @afteramount="addTotal"
              @beforeqty="deductQty"
              @afterqty="addQty"
              style="font-size: 16px;"
              >
            </tr>
          <tr v-if="!covered">
            <td></td>
            <td class="text-center">Delivery Fees</td>
            <td></td>
            <td>
                <input type="text" name="delivery" v-model="delivery.toFixed(2)" class="input-sm form-control text-right" readonly="readonly">
            </td>
          </tr>
          <tr>
            <td></td>
            <td class="text-center"><strong>Total</strong></td>
            <td>
              <strong>
                <input type="text" name="totalqty" class="input-sm form-control text-center" v-model="totalqty" readonly="readonly">
              </strong>
            </td>
            <td>
              <strong>
                <input type="text" name="total" class="input-sm form-control text-right" v-model="total" readonly="readonly">
              </strong>
            </td>
          </tr>
        </table>
        </div>
        <div class="form-group pull-right">
          <button class="btn btn-lg btn-success" v-bind:class="{'disabled': total==0}" style="border-radius: 5px;" v-if="!step3" @click="fillForm">Next <i class="fa fa-spinner fa-spin" v-if="loading"></i></button>
        </div>
      </div>
    </div>

    <div v-if="step3">
      <div class="col-md-12 col-xs-12">
        <h3 style="color:#323299;">Step 3 / 3</h3>
          <div class="row">
          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::text('name', null, ['class'=>'form-control', 'v-model'=>'form.name']) !!}
            </div>
          </div>

          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              {!! Form::label('contact', 'Contact Number', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::text('contact', null, ['class'=>'form-control', 'v-model'=>'form.contact']) !!}
            </div>
          </div>

          <div class="col-md-4 col-xs-12">
            <div class="form-group">
              {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::email('email', null, ['class'=>'form-control', 'v-model'=>'form.email']) !!}
            </div>
          </div>

          <div class="col-md-6 col-xs-6">
            <div class="form-group">
              {!! Form::label('street', 'Street Name', ['class'=>'control-label']) !!}
              {!! Form::text('street', null, ['class'=>'form-control', 'v-model'=>'form.street']) !!}
            </div>
          </div>

          <div class="col-md-6 col-xs-6">
            <div class="form-group">
              {!! Form::label('postcode', 'PostCode', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::text('postcode', null, ['class'=>'form-control', 'v-model'=>'form.postcode']) !!}
            </div>
          </div>

          <div class="col-md-4 col-xs-6">
            <div class="form-group">
              {!! Form::label('block', 'Block', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::text('block', null, ['class'=>'form-control', 'v-model'=>'form.block']) !!}
            </div>
          </div>

          <div class="col-md-4 col-xs-6">
            <div class="form-group">
              {!! Form::label('floor', 'Floor', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::text('floor', null, ['class'=>'form-control', 'v-model'=>'form.floor']) !!}
            </div>
          </div>

          <div class="col-md-4 col-xs-6">
            <div class="form-group">
              {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
              {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
              {!! Form::text('unit', null, ['class'=>'form-control', 'v-model'=>'form.unit']) !!}
            </div>
          </div>

          <div class="col-md-6 col-xs-12 form-group">
            {!! Form::label('del_date', 'Preferred Delivery Day:', ['class'=>'control-label']) !!}
            <select2 name="del_date[]" v-model="form.del_date" :options="deldate_option"></select2>
          </div>

          <div class="col-md-6 col-xs-12 form-group">
            {!! Form::label('del_time', 'Preferred Delivery Timeslot:', ['class'=>'control-label']) !!}
            <select2 name="del_time[]" v-model="form.del_time" :options="deltime_option"></select2>
            <small>{!! Form::label('del', '** Final Timing will be Confirmed via Phone/ SMS', ['class'=>'control-label', 'style'=>'color:red;']) !!}</small>
          </div>

          <div class="col-md-12 col-xs-12 form-group">
            {!! Form::label('remark', 'Remark (Optional)', ['class'=>'control-label']) !!}
            {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2', 'v-model'=>'form.remark']) !!}
          </div>

          <div class="col-md-12 col-xs-12" style="padding-top:20px;">
            <div class="form-group pull-right">
              <button type="submit" class="btn btn-lg btn-success" style="border-radius: 5px;">Confirm</button>
            </div>
          </div>
          </div>
      </div>
    </div>
    {!! Form::close() !!}

  </div>
</div>
</template>

<template id="select2-template">
  <select>
    <slot></slot>
  </select>
</template>

<template id="item-template">
  <tr>
    <td class="col-md-1 text-center">
      @{{number}}
    </td>
    <td class="col-md-6 text-left">
{{--       <div class="thumbnail">
        <img v-bind:src="item.main_imgpath" class="img-responsive">
      </div> --}}
      @{{item.caption}}
    </td>
    <td class="hidden">
      <input type="text" class="hidden" name="idArr[]" v-model="item.id">
    </td>
    <td class="hidden">
      <input type="text" class="hidden" name="captionArr[]" v-model="item.caption">
    </td>
    <td class="col-md-2 text-center">
      <select2 name="qtyArr[]" v-model="qty" :options="options"></select2>
    </td>
    <td class="col-md-2 text-center">
      <input type="text" name="amountArr[]" v-model="amount" class="input-sm form-control text-right" readonly="readonly" />
    </td>
  </tr>
</template>

<script src="/js/vue-controller/d2dorderController.js"></script>
@stop