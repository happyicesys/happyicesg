@inject('roles', 'App\Role')

<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
            {!! Form::text('name', null, ['class'=>'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
            {!! Form::email('email', null, ['class'=>'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('contact', 'Phone', ['class'=>'control-label']) !!}
            {!! Form::text('contact', null, ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-sm-8 col-xs-12">
        <div class="form-group">
            {!! Form::label('username', 'Username', ['class'=>'control-label']) !!}
            {!! Form::text('username', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('user_code', 'User ID', ['class'=>'control-label']) !!}
            {!! Form::text('user_code', null, ['class'=>'form-control']) !!}
        </div>
    </div>
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
    {!! Form::label('role', 'Position', ['class'=>'control-label']) !!}
    {!! Form::select('role_list[]', $roles::lists('label', 'id'), null, ['id'=>'role', 'class'=>'select form-control']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('can_access_inv', $user->can_access_inv) !!}
    {!! Form::label('can_access_inv', 'Can Access Inventory', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
</div>

@section('footer')
<script>
    $('#role').select2({
        tags:false,
        placeholder: 'Select...'
    });
</script>
@stop