@inject('units', 'App\Unit')

<div class="col-md-12 col-sm-12 col-xs-12">


    <div class="row" style="padding-bottom: 20px;">
        @if($item->main_imgpath)
            <div class="form-group">
                <div class="col-md-5 col-md-offset-1">
                    <img src="{{$item->main_imgpath}}" height="250" width="250" style="border:2px solid black">
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        {!! Form::label('main_imgpath', 'Change Photo', ['class'=>'control-label']) !!}
                        {!! Form::file('main_imgpath', ['class'=>'field']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('main_imgcaption', 'Caption', ['class'=>'control-label']) !!}
                        {!! Form::textarea('main_imgcaption', null, ['class'=>'form-control', 'rows'=>'2']) !!}
                    </div>
                </div>
            </div>
        @else
            <div class="form-group">
                <div class="col-md-5 col-md-offset-1">
                    <img src="{{$item->main_imgpath}}" alt="No photo found" height="250" width="250" style="border:2px solid black">
                </div>

                <div class="col-md-5">
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
        @endif
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

    <div class="form-group">
        {!! Form::label('remark', 'Description', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                {!! Form::select('unit', $units::lists('name', 'name'), null, ['id'=>'unit', 'class'=>'select form-control']) !!}
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('unit_cost', 'Cost Price', ['class'=>'control-label']) !!}
                {!! Form::text('unit_cost', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group" style="padding:20px 0px 20px 0px;">
                {!! Form::checkbox('publish', $item->publish) !!}
                {!! Form::label('publish', 'Publish Ecommerce', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group" style="padding:20px 0px 20px 0px;">
                {!! Form::checkbox('is_inventory', $item->is_inventory) !!}
                {!! Form::label('is_inventory', 'Inventory Count', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group" style="padding:20px 0px 20px 0px;">
                {!! Form::checkbox('is_commission', $item->is_commission) !!}
                {!! Form::label('is_commission', 'Is Commission', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
            </div>
        </div>
    </div>

</div>

@section('footer')
<script>
    $('.select').select2();
</script>
@stop
