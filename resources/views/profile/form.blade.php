@inject('payterms', 'App\Payterm')

<div class="col-md-8 col-md-offset-2">
    <div class="row">
        <div class="col-md-7 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                {!! Form::text('name', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="col-md-5 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('acronym', 'Alias', ['class'=>'control-label']) !!}
                {!! Form::text('acronym', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>
                    @if($profile->gst)
                        Co. Reg & GST Reg No
                    @else
                        Co. Reg No
                    @endif
                </label>
                {!! Form::text('roc_no', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-8 col-sm-6 col-xs-12" style="padding:30px 0px 0px 50px;">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    {!! Form::checkbox('gst', $profile->gst) !!}
                    {!! Form::label('gst', 'GST', ['class'=>'control-label', 'style'=>'padding-left:20px;']) !!}
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    {!! Form::checkbox('is_gst_inclusive', $profile->is_gst_inclusive) !!}
                    {!! Form::label('is_gst_inclusive', 'GST Inclusive', ['class'=>'control-label', 'style'=>'padding-left:20px;']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('gst_rate', 'GST Rate (%)', ['class'=>'control-label']) !!}
        {!! Form::text('gst_rate', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('address', 'Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('address', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('attn', 'Attn', ['class'=>'control-label']) !!}
                {!! Form::text('attn', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
                {!! Form::text('contact', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('alt_contact', 'Alt. Contact', ['class'=>'control-label']) !!}
                {!! Form::text('alt_contact', null, ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                {!! Form::email('email', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('payterm_id', 'Terms', ['class'=>'control-label']) !!}
        {!! Form::select('payterm_id', $payterms::pluck('name', 'id'), null, ['id'=>'payterm', 'class'=>'select form-control']) !!}
    </div>

{{--     <div class="form-group">
        {!! Form::label('logo', 'Logo', ['class'=>'control-label']) !!}
        {!! Form::file('logo', ['class'=>'form-control', 'name'=>'logo']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('header', 'Header', ['class'=>'control-label']) !!}
        {!! Form::file('header', ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('footer', 'Footer', ['class'=>'control-label']) !!}
        {!! Form::file('footer', ['class'=>'form-control']) !!}
    </div>   --}}
</div>


