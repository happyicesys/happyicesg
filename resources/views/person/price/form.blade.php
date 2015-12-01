@inject('items', 'App\Item')

<div class="col-md-10 col-md-offset-1" style="padding-top:20px">
    <ul class="list-group">
        <li class="list-group-item row">

        @unless($disabled)
            <div class="form-group">
                {!! Form::label('item_id', 'Item', ['class'=>'control-label']) !!}
                {!! Form::select('item_id', 
                    $items::select(DB::raw("CONCAT(product_id,' - ',name,' - ',remark) AS full, id"))->lists('full', 'id'), 
                    null, 
                    ['id'=>'unit', 'class'=>'select form-control']) 
                !!}
            </div> 
        @endunless    

            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('retail_price', 'Retail Price ($)', ['class'=>'control-label']) !!}
                    {!! Form::text('retail_price', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('quote_price', 'Quote Price ($)', ['class'=>'control-label']) !!}
                    {!! Form::text('quote_price', null, ['class'=>'form-control', 'placeholder'=>'(Optional) Fill to Override Retail x Cost Rate Calculation']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
                {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
            </div>  
        </li>
    </ul>             
</div>


@section('footer')
<script>
    $('.select').select2();           
</script>
@stop
