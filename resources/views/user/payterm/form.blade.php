<div class="col-md-8 col-md-offset-2">

    <div class="form-group">
        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('desc', 'Desc', ['class'=>'control-label']) !!}
        {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>             
    
</div>

