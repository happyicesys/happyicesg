@inject('countries', 'App\Country')
@inject('roles', 'App\Role')
@inject('franchisees', 'App\User')
@inject('trucks', 'App\Truck')

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

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('password', 'Password', ['class'=>'control-label']) !!}
            {!! Form::password('password', ['class'=>'form-control', 'placeholder'=>$pass_text]) !!}
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('password_confirmation', 'Password Confirmation', ['class'=>'control-label']) !!}
            {!! Form::password('password_confirmation', ['class'=>'form-control', 'placeholder'=>$pass_text]) !!}
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('role', 'Position', ['class'=>'control-label']) !!}
            {!! Form::select('role_list[]', $roles::lists('label', 'id'), null, ['id'=>'role', 'class'=>'select form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('sex_id', 'Sex', ['class'=>'control-label']) !!}
            <select name="sex_id" class="select form-control">
                <option value="">Select..</option>
                @foreach(\App\User::SEXES as $index => $sex)
                    <option value="{{$index}}" {{isset($user) && ($user->sex_id == $index) ? 'selected' : ''}}>
                        {{$sex}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('dob', 'D.O.B.', ['class'=>'control-label']) !!}
            {!! Form::date('dob', null, ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('fin_no', 'FIN No', ['class'=>'control-label']) !!}
            {!! Form::text('fin_no', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('permit_no', 'Permit No', ['class'=>'control-label']) !!}
            {!! Form::text('permit_no', null, ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('permit_expiry_date', 'Permit Expiry', ['class'=>'control-label']) !!}
            {!! Form::date('permit_expiry_date', null, ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('birth_country_id', 'Country of Birth', ['class'=>'control-label']) !!}
            {!! Form::select('birth_country_id', [''=>'Select..']+$countries::orderBy('name')->pluck('name', 'id')->all(), null, ['class'=>'select form-control']) !!}
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('nationality_country_id', 'Nationality', ['class'=>'control-label']) !!}
            {!! Form::select('nationality_country_id', [''=>'Select..']+$countries::orderBy('name')->pluck('nationality_name', 'id')->all(), null, ['class'=>'select form-control']) !!}
        </div>
    </div>
</div>




@if($user->hasRole('franchisee'))
    <div class="form-group">
        {!! Form::label('company_name', 'Company Name', ['class'=>'control-label']) !!}
        {!! Form::text('company_name', null, ['class'=>'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('bill_address', 'Bill Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('bill_address', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>
@endif

@if($user->hasRole('subfranchisee'))
    <div class="form-group">
        {!! Form::label('master_franchisee_id', 'Master Franchisee', ['class'=>'control-label search-title']) !!}
        {!! Form::select('master_franchisee_id', [''=>'All']+$franchisees::filterUserFranchise()->select(DB::raw("CONCAT(id,user_code,' (',name,')') AS full, id"))->orderBy('user_code')->pluck('full', 'id')->all(), null, ['id'=>'master_franchisee_id',
            'class'=>'select form-control'
            ])
        !!}
    </div>
@endif

<div class="form-group">
    {!! Form::checkbox('can_access_inv', $user->can_access_inv) !!}
    {!! Form::label('can_access_inv', 'Can Access Inventory', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
</div>

<hr>

<div class="form-group">
    {!! Form::label('truck_id', 'Binded Truck', ['class'=>'control-label']) !!}
    <select name="truck_id" class="select form-control">
        <option value="">Not Applicable</option>
        @foreach($trucks::orderBy('name')->get() as $truck)
            <option value="{{$truck->id}}" {{isset($user) && $user->truck()->exists() && ($user->truck->id == $truck->id) ? 'selected' : ''}}>
                {{$truck->name}} @if($truck->desc) ({{$truck->desc}}) @endif
            </option>
        @endforeach
    </select>
</div>

@section('footer')
<script>
    $('.select').select2({
        tags:false,
        placeholder: 'Select...'
    });
</script>
@stop