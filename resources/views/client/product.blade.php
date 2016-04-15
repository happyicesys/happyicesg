@extends('template_client')
@section('title')
Healthier Life
@stop
@section('content')

    <div class=" text-center" style="padding: 50px 0px 40px 0px;">
        <span class="row" style="font-size: 18px;">
            Ice cream products from Happy Ice are categorized as Healthier Snack by Singapore Health Promotion Board.
        </span>
    </div>

    <div ng-app="app" ng-controller="clientMainController">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12" style="margin: 0px 20px 0px 0px;">
                <div dir-paginate="product in products | itemsPerPage:itemsPerPage"  current-page="currentPage" class="col-md-4 col-sm-4 col-xs-4" style="font-size:20px;">
                    <img class="img-responsive img-center" ng-src="@{{product.main_imgpath}}" ng-alt="@{{product.main_imgcaption}}" style="max-height: 350px;">
                    <p class="product-name text-center">@{{product.name}}</p>
                </div>

            </div>
        </div>
{{--
        <div class="row text-center" style="padding: 30px 0px 100px 0px; color:blue;">
            <div class="col-md-12 col-sm-12 col-xs-12" style="font-size: 16px;">
                <button ng-if="products.length > 3" ng-click="showAllProduct()" class="btn btn-primary thick-border" style="padding: 15px 50px 15px 50px; font-size:18px;" ng-model="productModel">@{{productText}}</button>
            </div>
        </div> --}}
    </div>

@stop

@section('footer')
    <script src="/js/client_index.js"></script>
@stop