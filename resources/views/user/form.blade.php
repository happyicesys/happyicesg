@inject('roles', 'App\Role')

<div class="col-md-8 col-md-offset-2">
    <div class="form-group">
        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('username', 'Username', ['class'=>'control-label']) !!}
        {!! Form::text('username', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::email('email', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('password', 'Password', ['class'=>'control-label']) !!}
        {!! Form::password('password', ['class'=>'form-control', 'placeholder'=>$pass_text]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('password_confirmation', 'Password Confirmation', ['class'=>'control-label']) !!}
        {!! Form::password('password_confirmation', ['class'=>'form-control', 'placeholder'=>$pass_text]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('contact', 'Phone', ['class'=>'control-label']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('role', 'Position', ['class'=>'control-label']) !!}
        {!! Form::select('role_list[]', $roles::lists('label', 'id'), null, ['id'=>'role', 'class'=>'select form-control']) !!}
    </div>
</div>

@section('footer')
<script>
    $('#role').select2({
        tags:false,
        placeholder: 'Select...'
    });
</script>
@stop