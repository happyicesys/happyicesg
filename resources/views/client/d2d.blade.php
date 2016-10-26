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
      <div class="col-md-6 col-xs-12">
        <a href="#" class="portfolio-box">
          <img src="img/d2d/a5 menu_final.jpg" class="img-responsive" alt="door to door ice cream delivery menu">
          <div class="portfolio-box-caption-content">
              <div class="project-category text-faded">
              </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xs-12">
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
          <h3 style="color:#323299;">Step 1 / 2</h3>
          <p>Please enter your postcode</p>
        </div>
      </div>
        <div class="row">
          <div class="col-md-6 col-xs-12">
            <div class="form-bottom">
              <div class="form-group">
                <input type="text" name="postcode" placeholder="Postcode..." class="form-first-name form-control input-lg" v-model="form.postcode">
              </div>
              <span v-if="formErrors['postcode']" class="help-block" style="color:red;">
                <ul class="row">
                    <li style="color:red;">@{{ formErrors['postcode'] }}</li>
                </ul>
              </span>
              <button type="button" class="btn btn-success btn-next btn-lg" @click="verifyPostcode">Next <i class="fa fa-spinner fa-spin" v-if="loading_postcode"></i></button>
            </div>
          </div>
        </div>
    </fieldset>
  </div>
</div>
</template>

<script src="/js/vue-controller/d2dorderController.js"></script>
@stop