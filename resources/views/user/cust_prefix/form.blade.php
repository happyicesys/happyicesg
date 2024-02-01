@inject('trucks', 'App\CustPrefix')

<div class="form-group">
    {!! Form::label('code', 'Prefix', ['class'=>'control-label']) !!}
    {!! Form::label('required', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
    {!! Form::text('code', null, ['class'=>'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('desc', 'Desc', ['class'=>'control-label']) !!}
    {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'3']) !!}
</div>

<script>
    $('.select').select2({
        placeholder: 'Select...'
    });
</script>