@inject('items', 'App\Item')
<div class="col-md-10 col-md-offset-1">
    <div class="form-group">
        {!! Form::label('sequence', 'Sequence number', ['class'=>'control-label']) !!}
        {!! Form::text('sequence', null, ['class'=>'form-control', 'placeholder'=>'Leave blank for auto sequence']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('item_id', 'Item', ['class'=>'control-label']) !!}
        {!! Form::select('item_id',
            [''=>null] + $items::select(DB::raw("CONCAT(product_id,' - ',name) AS full, id"))->orderBy('product_id')->pluck('full', 'id')->all(),
            null,
            ['class'=>'select form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('caption', 'Caption & price for Dtd', ['class'=>'control-label']) !!}
        {!! Form::text('caption', null, ['class'=>'form-control']) !!}
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('unit_price', 'Unit Price', ['class'=>'control-label']) !!}
                {!! Form::text('unit_price', null, ['class'=>'form-control', 'placeholder'=>'Numeric']) !!}
            </div>
        </div>

        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('qty_divisor', 'Qty Divisor (1/Qty_divisor)', ['class'=>'control-label']) !!}
                {!! Form::text('qty_divisor', null, ['class'=>'form-control', 'placeholder'=>'Numeric']) !!}
            </div>
        </div>
    </div>
</div>

@section('footer')
<script>
    $('.select').select2();
</script>
@stop
