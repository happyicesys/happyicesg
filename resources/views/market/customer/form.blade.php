@inject('payterm', 'App\Payterm')
@inject('people', 'App\Person')

<div class="col-md-6">

    <div class="form-group">
        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('alt_contact', 'Alt Contact', ['class'=>'control-label']) !!}
        {!! Form::text('alt_contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::email('email', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('payterm', 'Terms', ['class'=>'control-label']) !!}
        {!! Form::select('payterm', $payterm::lists('name', 'name'), null, ['id'=>'payterm', 'class'=>'select form-control']) !!}
    </div>

</div>

<div class="col-md-6">

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('block', 'Block', ['class'=>'control-label']) !!}
                {!! Form::text('block', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('floor', 'Floor', ['class'=>'control-label']) !!}
                {!! Form::text('floor', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                {!! Form::text('unit', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('del_address', 'Delivery Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('del_address', null, ['class'=>'form-control', 'rows'=>'1']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('del_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
        {!! Form::text('del_postcode', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
    </div>

    @if(Auth::user()->hasRole('admin'))
        <div class="form-group">
            {!! Form::label('parent_id', 'Manager', ['class'=>'control-label']) !!}
            {!! Form::select('parent_id', [''=>null] + $people::select(DB::raw("CONCAT(name,' - ',cust_id,' (',cust_type,')') AS full, id"))->where('cust_id', 'LIKE', 'D%')->orderBy('cust_type', 'desc')->lists('full', 'id')->all(), null, ['class'=>'select form-control']) !!}
        </div>
    @elseif(!Auth::user()->hasRole('admin') and $people::whereUserId(Auth::user()->id)->first())
        <div class="form-group">
            {!! Form::label('parent_id', 'Manager', ['class'=>'control-label']) !!}
            {!! Form::select('parent_id', [''=>null] + $people::whereUserId(Auth::user()->id)->first()->descendants()->select(DB::raw("CONCAT(name,' - ',cust_id,' (',cust_type,')') AS full, id"))->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_type', 'desc')->lists('full', 'id')->all(), null, ['class'=>'select form-control']) !!}
        </div>
    @endif

</div>

<script>
    $('.select').select2();
</script>
