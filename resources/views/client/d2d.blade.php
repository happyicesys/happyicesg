@inject('salesitems', 'App\D2dOnlineSale')

@extends('template_client')
@section('title')
Door To Door
@stop
@section('content')
<div id="d2dorderController">

<section id="portfolio" style="padding:10px 5px 10px 0px;">
  <div class="container-fluid">
    <order></order>
  </div>
</section>
</div>
<template id="order-template">
    <div class="row no-gutter">
      <div class="col-md-5 col-xs-12">
        <a href="#" class="portfolio-box">
          <img src="img/d2d/general_d2d.jpg" class="img-responsive" alt="HappyIce Door to Door" v-if="step1">
          <img src="img/d2d/a5 menu_final.jpg" class="img-responsive" alt="door to door ice cream delivery menu" v-if="!step1 && covered">
          <img src="img/d2d/all_item_2016.jpg" class="img-responsive" alt="HappyIce D2d All Item" v-if="!step1 && !covered">
          <div class="portfolio-box-caption-content">
              <div class="project-category text-faded">
              </div>
          </div>
        </a>
      </div>
      <div class="col-md-7 col-xs-12">

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
                <p style="color:red;" v-if="covered">**Congrats, you are within the Door to door coverage area, will be entitled free delivery service.**</p>
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
                <button class="btn btn-lg btn-success" v-bind:class="{'disabled': disableNext}" style="border-radius: 5px;" v-if="!step3" @click="fillForm">Next <i class="fa fa-spinner fa-spin" v-if="loading"></i></button>
              </div>
            </div>
          </div>

          <div v-if="step3">
            <div class="col-md-12 col-xs-12">
              <h3 style="color:#323299;">Step 3 / 3</h3>
                <div class="row">
                <div class="col-md-4 col-xs-12">
                  <div class="form-group" :class="{ 'has-error' : formErrors['name'] }">
                    <label for="name" class="control-label">Name</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.name">*</label>
                    <input type="text" name="name" class="form-control" v-model="form.name" @keyup="validateOrder">
                    <span v-if="formErrors['name']" class="help-block" style="color:red;">
                      <ul class="row">
                          <li style="color:red;">@{{ formErrors['name'][0] }}</li>
                      </ul>
                    </span>
                  </div>
                </div>

                <div class="col-md-4 col-xs-12">
                  <div class="form-group" :class="{ 'has-error' : formErrors['contact'] }">
                    <label for="contact" class="control-label">Handphone Number</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.contact">*</label>
                    <input type="text" name="contact" class="form-control" v-model="form.contact" @keyup="validateOrder">
                    <span v-if="formErrors['contact']" class="help-block" style="color:red;">
                      <ul class="row">
                          <li style="color:red;">@{{ formErrors['contact'][0] }}</li>
                      </ul>
                    </span>
                  </div>
                </div>

                <div class="col-md-4 col-xs-12">
                  <div class="form-group" :class="{ 'has-error' : formErrors['email'] }">
                    <label for="email" class="control-label">Email</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.email">*</label>
                    <input type="email" name="email" class="form-control" v-model="form.email" @keyup="validateOrder">
                    <span v-if="formErrors['email']" class="help-block" style="color:red;">
                      <ul class="row">
                          <li style="color:red;">@{{ formErrors['email'][0] }}</li>
                      </ul>
                    </span>
                  </div>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6 col-xs-6">
                  <div class="form-group">
                    <label for="street" class="control-label">Street</label>
                    <input type="text" name="street" class="form-control" v-model="form.street">
                  </div>
                </div>

                <div class="col-md-6 col-xs-6">
                  <div class="form-group" :class="{ 'has-error' : formErrors['postcode'] }">
                    <label for="postcode" class="control-label">Postcode</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.postcode">*</label>
                    <input type="text" name="postcode" class="form-control" v-model="form.postcode" @change="verifyPostcode">
                    <span v-if="formErrors['postcode']" class="help-block" style="color:red;">
                      <ul class="row">
                          <li style="color:red;">@{{ formErrors['postcode'][0] }}</li>
                      </ul>
                    </span>
                  </div>
                </div>
                </div>

                <div class="row">
                <div class="col-md-4 col-xs-6">
                  <div class="form-group">
                    <label for="block" class="control-label">Block</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.block">*</label>
                    <input type="text" name="block" class="form-control" v-model="form.block">
                  </div>
                </div>

                <div class="col-md-4 col-xs-6">
                  <div class="form-group">
                    <label for="floor" class="control-label">Floor</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.floor">*</label>
                    <input type="text" name="floor" class="form-control" v-model="form.floor">
                  </div>
                </div>

                <div class="col-md-4 col-xs-6">
                  <div class="form-group">
                    <label for="unit" class="control-label">Unit</label>
                    <label for="required" class="control-label" style="color:red;" v-if="!form.unit">*</label>
                    <input type="text" name="unit" class="form-control" v-model="form.unit">
                  </div>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6 col-xs-12 form-group">
                  <label for="del_date" class="control-label">Preferred Delivery Day:</label>
                  <select2 name="del_date[]" v-model="form.del_date" :options="deldate_option"></select2>
                </div>

                <div class="col-md-6 col-xs-12 form-group">
                  <label for="del_time" class="control-label">Preferred Delivery Timeslot:</label>
                  <select2 name="del_time[]" v-model="form.del_time" :options="deltime_option"></select2>
                  <small>{!! Form::label('del', '** Final Timing will be Confirmed via Phone/ SMS', ['class'=>'control-label', 'style'=>'color:red;']) !!}</small>
                </div>
                </div>

                <div class="row">
                <div class="col-md-12 col-xs-12 form-group">
                  <label for="remark" class="control-label">Remark (Optional)</label>
                  <textarea name="remark" class="form-control" rows="2" v-model="form.remark"></textarea>
                </div>
                </div>

                <div class="row">
                <div class="col-md-12 col-xs-12" style="padding-top:20px;">
                  <div class="form-group pull-right">
                    <button
                      type="submit"
                      class="btn btn-lg btn-success"
                      style="border-radius: 5px;"
                      v-bind:class="{'disabled' : !submitable}"
                    >
                      Confirm
                    </button>
                  </div>
                </div>
                </div>
            </div>
          </div>
          {!! Form::close() !!}

        </div>
      </div>
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