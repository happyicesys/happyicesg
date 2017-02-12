<div class="form-group">
    {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
    {!! Form::label('required', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
    {!! Form::text('name', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('desc', 'Description', ['class'=>'control-label']) !!}
    {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'2']) !!}
</div>

