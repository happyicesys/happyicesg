@extends('template_client')
@section('title')
    Healthier Life
@stop
@section('content')
    <div id="clientProductController" style=" padding-top: 20px; background-color: #f8eee7;" >
        <productpage inline-template>
        <div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class=" text-center" style="padding: 20px 0px 0px 0px;">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <img src="/img/Happy-Ice-Logo.png" class="img-responsive center-block">
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <img src="/img/official_icon/Healthier_choice_logo.png" class="img-responsive pull-right" height="80" width="80">
                        </div>
                        <div class="col-md-2 col-sm-2 hidden-xs" style="padding-top: 20px; color: #fe7ecc;">
                            <i class="fa fa-arrow-left fa-2x" style="vertical-align: bottom;"></i>
                        </div>
                        <div class="col-md-2 col-sm-2 hidden-xs" style="padding-top: 20px; color: #fe7ecc;">
                            <i class="fa fa-arrow-right fa-2x" style="vertical-align: bottom;"></i>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <img src="/img/official_icon/Halal.png" class="img-responsive pull-left" style="padding-top: 6px;" height="70" width="70">
                        </div>
                    </div>
                    <span class="row" style="font-size: 18px;">
                        Ice cream products from Happy Ice are categorized as Healthier Snack by Singapore Health Promotion Board.
                    </span>
                </div>
            </div>
            <div class="row" style="padding-top: 20px; margin-left: 10px; margin-right: 10px;">
                <div class="col-md-3 col-sm-3 col-xs-12" style="font-size: 18px;">
                    <div class="panel panel-primary">
                        <div class="panel-body" style="background-color: #f4decb;">
                            <ul class="nav nav-stacked nav-pills">
                                <li v-for="(itemcategory, index) in itemcategories" :class="{'active': index === 0}" v-if="itemcategory.items.length > 0">
                                    <a href="" data-toggle="tab" style="color: #49274A" class="text-center" @click="loadProductsByItemcategory(itemcategory.id)">
                                        @{{itemcategory.name}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div v-for="(product, index) in products" class="col-md-4 col-sm-4 col-sx-12" :class="{'row' : index % 3 == 0}">
                                    <div  class="thumbnail" style="font-size:20px;">
                                            <img class="img-responsive center-block" :src="product.main_imgpath" :alt="product.main_imgcaption" style="max-height: 350px;">

                                            <div class="col-md-12 col-sm-12 col-xs-12" style="background-color: #f4decb; margin-top: -20px;" v-if="product.is_healthier || product.is_halal">
                                                <span class="col-md-6 col-sm-6 col-xs-6" v-if="product.is_healthier">
                                                    <img src="/img/official_icon/Healthier_choice_logo.png" class="img-responsive pull-right" height="50" width="50" style="margin-top: -3px;">
                                                </span>
                                                <span class="col-md-6 col-sm-6 col-xs-6" v-if="product.is_halal">
                                                    <img src="/img/official_icon/Halal.png" class="img-responsive pull-left" height="42" width="42" style="margin-top: 2px;">
                                                </span>

                                            </div>
                                            {{-- <div class="caption"> --}}
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12" v-if="product.nutri_imgpath">
                                                    <img :src="product.nutri_imgpath" class="img-responsive" max-height="220" max-width="320" style="border: thin solid black">
                                                </div>
                                                    <p class="product-name text-center" style="font-size: 17px;" v-if="product.main_imgpath">@{{product.name}}</p>
                                            </div>
                                            {{-- </div> --}}
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </productpage>
    </div>
@stop

@section('footer')
    <script src="/js/vue-controller/clientProductController.js"></script>
@stop