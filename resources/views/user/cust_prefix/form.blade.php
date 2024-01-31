@inject('trucks', 'App\CustPrefix')

<div class="form-group">
    {!! Form::label('code', 'Prefix', ['class'=>'control-label']) !!}
    {!! Form::label('required', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
    {!! Form::text('code', null, ['class'=>'form-control']) !!}
</div>

<script>
    $('.select').select2({
        placeholder: 'Select...'
    });
</script>