@inject('units', 'App\Unit')

<div class="col-md-8 col-md-offset-2">

    <div class="form-group">
        {!! Form::label('product_id', 'ID', ['class'=>'control-label']) !!}
        {!! Form::text('product_id', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('name', 'Product', ['class'=>'control-label']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('remark', 'Description', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
        {!! Form::select('unit', $units::lists('name', 'name'), null, ['id'=>'unit', 'class'=>'select form-control']) !!}
    </div>        
    
</div>

@section('footer')
<script>
    $('.select').select2();           
</script>
@stop
