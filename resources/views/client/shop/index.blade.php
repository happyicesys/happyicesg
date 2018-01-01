@inject('generalsettings', 'App\GeneralSetting')

@extends('template_client')
@section('title')
{{ $SHOP_TITLE }}
@stop
@section('content')

    @php
        $general_setting = $generalsettings::firstOrFail();
    @endphp

    @if($general_setting->ecommerce_info)
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="alert alert-info alert-important">
            <pre>
                {{$general_setting->ecommerce_info}}
            </pre>
        </div>
    </div>
    @endif
    <div id="shopController">
        <div class="container">
            <shop></shop>
        </div>
    </div>
    <template id="shop-template">
        <div class="form-group">
            <label class="control-label">
                Choose your Area
            </label>
            <select class="form-control" name="area_id" v-model="area_id">

            </select>
        </div>
    </template>

    <script src="/js/vue-controller/d2dorderController.js"></script>
@stop