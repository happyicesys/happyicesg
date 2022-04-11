@inject('zones', 'App\Zone')

<div class="form-group">
    {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
    {!! Form::label('required', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
    {!! Form::text('name', null, ['class'=>'form-control']) !!}
</div>