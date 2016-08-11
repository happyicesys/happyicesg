@inject('payterm', 'App\Payterm')
@inject('members', 'App\Person')

<div class="col-md-6">
{{--
    <div class="form-group">
        {!! Form::label('cust_id', 'ID', ['class'=>'control-label']) !!}
        {!! Form::text('cust_id', null, ['class'=>'form-control']) !!}
    </div> --}}
    @if(isset($self))
    <div class="form-group">
        {!! Form::label('name', 'ID Name', ['class'=>'control-label']) !!}
        <a href="/person/{{$self->id}}/edit">{!! Form::text('name', null, ['class'=>'form-control', 'readonly'=>'readonly']) !!}</a>
    </div>
    @else
    <div class="form-group">
        {!! Form::label('name', 'ID Name', ['class'=>'control-label']) !!}
        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>
    @endif

    <div class="form-group">
        {!! Form::label('com_remark', 'Company', ['class'=>'control-label']) !!}
        {!! Form::text('com_remark', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('alt_contact', 'Alt Contact', ['class'=>'control-label']) !!}
        {!! Form::text('alt_contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
        {!! Form::email('email', null, ['class'=>'form-control']) !!}
    </div>

    @if(Auth::user()->hasRole('admin'))
    <div class="form-group">
        {!! Form::label('cost_rate', 'Cost Rate (%)', ['class'=>'control-label']) !!}
        {!! Form::text('cost_rate', null, ['class'=>'form-control', 'placeholder'=>'Leave Blank for 100% as Default']) !!}
    </div>
    @endif

</div>

<div class="col-md-6">

    @if(isset($self))
        <div class="form-group">
            {!! Form::label('company', 'Username', ['class'=>'control-label']) !!}
            {!! Form::text('company', null, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
        </div>
    @else
        <div class="form-group">
            {!! Form::label('company', 'Username', ['class'=>'control-label']) !!}
            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
            {!! Form::text('company', null, ['class'=>'form-control']) !!}
        </div>
    @endif

    <div class="form-group">
        {!! Form::label('bill_address', 'Billing Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('bill_address', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('del_address', 'Delivery Address', ['class'=>'control-label']) !!}
        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
        {!! Form::textarea('del_address', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('del_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
        {!! Form::text('del_postcode', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('payterm', 'Terms', ['class'=>'control-label']) !!}
        {!! Form::select('payterm', $payterm::lists('name', 'name'), null, ['id'=>'payterm', 'class'=>'select form-control']) !!}
    </div>

</div>

    <div class="row">

        <div class="col-md-12 col-xs-12">

            <div class="col-md-6 col-xs-12">

                <div class="form-group">

                    {!! Form::label('time_range', 'Available Time Range', ['class'=>'control-label']) !!}
                    {!! Form::textarea('time_range', null, ['class'=>'form-control', 'rows'=>'2']) !!}

                </div>

            </div>

            <div class="col-md-6 col-xs-12">

                <div class="form-group">

                    {!! Form::label('block_coverage', 'Block Coverage', ['class'=>'control-label']) !!}
                    {!! Form::textarea('block_coverage', null, ['class'=>'form-control', 'rows'=>'2']) !!}

                </div>

            </div>

        </div>

    </div>

    <div class="row"></div>

    <div class="col-md-12">

        @if(isset($person) and Auth::user()->hasRole('admin'))
        <hr>
        {{-- @if(isset($person)) --}}
            <div class="form-group">
                {!! Form::label('parent_id', 'Manager', ['class'=>'control-label']) !!}
                {!! Form::select('parent_id', [''=>null] + $members::where('cust_id', 'LIKE', 'D%')->where('active', 'Yes')->whereNotIn('id', [$person->id])->lists('name', 'id')->all(), null, ['id'=>'parent_id', 'class'=>'select form-control']) !!}
            </div>
        @elseif(isset($person))
        <hr>
            <div class="form-group">
                {!! Form::label('parent_id', 'Manager', ['class'=>'control-label']) !!}
                {!! Form::select('parent_id', [''=>null] + $members::where('cust_id', 'LIKE', 'D%')->where('active', 'Yes')->whereNotIn('id', [$person->id])->lists('name', 'id')->all(), null, ['id'=>'parent_id', 'class'=>'select form-control', 'disabled'=>'disabled']) !!}
            </div>
        @endif

        @if(Auth::user()->hasRole('admin'))
        <div class="form-group">
            {!! Form::label('cust_type', 'Role Level', ['class'=>'control-label']) !!}
            {!! Form::select('cust_type', [
                                    ''=>null,
                                    'OM' => 'OM',
                                    'OE' => 'OE',
                                    'AM' => 'AM',
                                    'AB' => 'AB',
            ], null, ['id'=>'parent_id', 'class'=>'select form-control']) !!}
        </div>
        @else
        <div class="form-group">
            {!! Form::label('cust_type', 'Role Level', ['class'=>'control-label']) !!}
            {!! Form::select('cust_type', [
                                    ''=>null,
                                    'OM' => 'OM',
                                    'OE' => 'OE',
                                    'AM' => 'AM',
                                    'AB' => 'AB',
            ], null, ['id'=>'parent_id', 'class'=>'select form-control', 'disabled'=>'disabled']) !!}
        </div>
        @endif

    </div>

<script>
    $('.select').select2({
        placeholder: 'Please Select...'
    });
</script>
