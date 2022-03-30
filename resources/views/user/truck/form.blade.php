@inject('trucks', 'App\Truck')

<div class="form-group">
    {!! Form::label('name', 'Plate Num', ['class'=>'control-label']) !!}
    {!! Form::label('required', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
    {!! Form::text('name', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('desc', 'Desc', ['class'=>'control-label']) !!}
    {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'2']) !!}
</div>

<div class="form-group">
    {!! Form::label('height', 'Height(m)', ['class'=>'control-label']) !!}
    {!! Form::text('height', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('iu_number', 'IU Number', ['class'=>'control-label']) !!}
    {!! Form::text('iu_number', null, ['class'=>'form-control']) !!}
</div>

<script>
    $('.select').select2({
        placeholder: 'Select...'
    });
</script>