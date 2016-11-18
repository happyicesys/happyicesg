@inject('items', 'App\Item')
@inject('people', 'App\Person')
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

<div class="row">
    <div class="col-md-8 col-xs-12">
        <div class="form-group">
            {!! Form::label('caption', 'Caption and price for Dtd', ['class'=>'control-label']) !!}
            {!! Form::text('caption', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('qty_divisor', 'Qty Divisor', ['class'=>'control-label']) !!}
            {!! Form::text('qty_divisor', null, ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

    <div class="row">
    <div class="col-md-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('person_id', 'Customer ID', ['class'=>'control-label']) !!}
            {!! Form::select('person_id',
                [''=>null] + $people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->pluck('full', 'id')->all(),
                null,
                ['class'=>'select form-control']) !!}
        </div>
    </div>

    <div class="col-md-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('coverage', 'D2D Coverage', ['class'=>'control-label']) !!}
            {!! Form::select('coverage',
                ['all'=>'All', 'within'=>'Within', 'without'=>'Without'],
                null,
                ['class'=>'select form-control']) !!}
        </div>
    </div>
    </div>
</div>

@section('footer')
<script>
    $('.select').select2({
        placeholder: 'Select..'
    });
</script>
@stop
