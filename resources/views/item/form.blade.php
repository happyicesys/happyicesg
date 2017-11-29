@inject('units', 'App\Unit')
@inject('itemcategories', 'App\Itemcategory')

<div class="col-md-12 col-sm-12 col-xs-12">


    <div class="row" style="padding-bottom: 20px;">
        @if($item->main_imgpath)
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <a href="{{$item->main_imgpath}}">
                            <img src="{{$item->main_imgpath}}" height="250" width="250" style="border:2px solid black">
                        </a>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('main_imgpath', 'Change Main Photo', ['class'=>'control-label']) !!}
                            {!! Form::file('main_imgpath', ['class'=>'field']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('main_imgcaption', 'Caption', ['class'=>'control-label']) !!}
                            {!! Form::textarea('main_imgcaption', null, ['class'=>'form-control', 'rows'=>'2']) !!}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <img src="{{$item->main_imgpath}}" alt="No photo found" height="250" width="250" style="border:2px solid black">
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('main_imgpath', 'Main Photo', ['class'=>'control-label']) !!}
                            {!! Form::file('main_imgpath', ['class'=>'field']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('main_imgcaption', 'Caption', ['class'=>'control-label']) !!}
                            {!! Form::textarea('main_imgcaption', null, ['class'=>'form-control', 'rows'=>'2']) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <hr size="3">
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="$('#desc_img').toggle();">
                    <i class="fa fa-caret-down"></i> Other Images
                </button>
            </div>

            <div id="desc_img">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            @if($item->nutri_imgpath)
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <img src="{{$item->nutri_imgpath}}" height="250" width="250" style="border:2px solid black">
                                    </div>

                                    <div class="col-md-5">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            {!! Form::label('nutri_imgpath', 'Change Nutrition Photo', ['class'=>'control-label']) !!}
                                            {!! Form::file('nutri_imgpath', ['class'=>'field']) !!}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <img src="{{$item->nutri_imgpath}}" alt="No photo found" height="250" width="250" style="border:2px solid black">
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {!! Form::label('nutri_imgpath', 'Nutrition Photo', ['class'=>'control-label']) !!}
                                            {!! Form::file('nutri_imgpath', ['class'=>'field']) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <hr size="3">
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('product_id', 'ID', ['class'=>'control-label']) !!}
                {!! Form::text('product_id', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('name', 'Product', ['class'=>'control-label']) !!}
                {!! Form::text('name', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('remark', 'Description', ['class'=>'control-label']) !!}
                {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'3']) !!}
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('productpage_desc', 'Product Page Desc', ['class'=>'control-label']) !!}
                {!! Form::textarea('productpage_desc', null, ['class'=>'form-control', 'rows'=>'3']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('base_unit', 'Pieces', ['class'=>'control-label']) !!}
                {!! Form::text('base_unit', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                {!! Form::select('unit', $units::lists('name', 'name'), null, ['id'=>'unit', 'class'=>'select form-control']) !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('unit_cost', 'Cost Price', ['class'=>'control-label']) !!}
                {!! Form::text('unit_cost', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('itemcategory_id', 'Item Category', ['class'=>'control-label']) !!}
                {!! Form::select('itemcategory_id', [''=>null] + $itemcategories::pluck('name', 'id')->all(), null, ['id'=>'unit', 'class'=>'select form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-2 col-sm-2 col-xs-6">
            <div class="form-group">
                {!! Form::checkbox('publish', $item->publish) !!}
                {!! Form::label('publish', 'Publish Ecommerce', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-6">
            <div class="form-group">
                {!! Form::checkbox('is_inventory', $item->is_inventory) !!}
                {!! Form::label('is_inventory', 'Inventory Count', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-6">
            <div class="form-group">
                {!! Form::checkbox('is_commission', $item->is_commission) !!}
                {!! Form::label('is_commission', 'Is Commission', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-6">
            <div class="form-group">
                {!! Form::checkbox('is_healthier', $item->is_healthier) !!}
                {!! Form::label('is_healthier', 'Is Healthier', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-6">
            <div class="form-group">
                {!! Form::checkbox('is_halal', $item->is_halal) !!}
                {!! Form::label('is_halal', 'Is Halal', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
    </div>

</div>

@section('footer')
<script>
    $('.select').select2({
        placeholder: 'Select..'
    });
    $('#desc_img').hide();
</script>
@stop
