<div class="panel">
    <div class="panel-heading">
        Vending Machine
    </div>

    <div class="panel-body">
{{--             <div class="col-md-12 col-sm-12 col-xs-12">
            <assignVending id="assignVendingController" :person_id={{json_encode($person->id)}} inline-template>
                @include('person._assignVending')
            </assignVending>
        </div> --}}
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-6 isVendDiv">
                <div class="form-group">
                    <input type="radio" name="type" value="is_dvm" {{$person->is_dvm ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
                    {!! Form::label('is_dvm', 'Direct Vending Machine', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
                </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-6 isVendDiv">
                <div class="form-group">
                    <input type="radio" name="type" value="is_vending" {{$person->is_vending ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
                    {!! Form::label('is_vending', 'Fun Vending Machine', ['class'=>'control-label', 'style'=>'padding-left:5px;', 'ng-model'=>'form.is_vending']) !!}
                </div>
            </div>

            @if(config('app.usage') != 'operator')
            <div class="col-md-4 col-sm-4 col-xs-6 isVendDiv">
                <div class="form-group">
                    <input type="radio" name="type" value="is_combi" {{$person->is_combi ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
                    {!! Form::label('is_combi', 'Combi', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group" style="padding-top: 20px;">
                    {!! Form::checkbox('is_commission_report', $person->is_commission_report, null, []) !!}
                    {!! Form::label('To Generate Commission Report', '', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 isVendDiv">
                <div class="form-group" style="padding-top: 20px;">
                    {!! Form::checkbox('is_sys', $person->is_sys, null, []) !!}
                    {!! Form::label('is_sys', 'Connect to sys.happyice?', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
                </div>
            </div>
        </div>
        {{-- <div class="form-group">
            {!! Form::label('vend_code', 'Vend ID#', ['class'=>'control-label']) !!}
            {!! Form::text('vend_code', null, ['class'=>'form-control']) !!}
        </div>
 --}}

        <div class="form-group">
            {!! Form::label('cooperate_method', 'Cooperate Method', ['class'=>'control-label']) !!}
            {!! Form::select('cooperate_method', ['1'=>'Profit Sharing', '2'=>'Rentals', '3' => 'Free Placement'], null, ['class'=>'selectnotclear form-control', 'id'=>'cooperate_method', 'disabled'=>$disabled]) !!}
        </div>

        <div class="row commissionDiv">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('commission_type', 'Commission Type', ['class'=>'control-label']) !!}
                    {!! Form::select('commission_type', ['1'=>'Absolute Amount', '2'=>'Percentage'], null, ['class'=>' selectnotclear form-control', 'disabled'=>$disabled]) !!}
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('commission_package', 'Commission Package', ['class'=>'control-label']) !!}
                    {!! Form::select('commission_package', ['1'=>'Both Utility & Comm', '2'=>'Whichever One is Higher'], null, ['class'=>'selectnotclear form-control', 'disabled'=>$disabled]) !!}
                </div>
            </div>
        </div>
        <div class="row">
        @if(!$person->is_dvm)
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('vending_piece_price', 'Price Per Piece ($)', ['class'=>'control-label']) !!}
                {!! Form::text('vending_piece_price', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        @endif
        <div class="col-md-4 col-sm-4 col-xs-12 rentalDiv">
            <div class="form-group">
                {!! Form::label('vending_monthly_rental', 'Monthly Rental ($)', ['class'=>'control-label']) !!}
                {!! Form::text('vending_monthly_rental', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        @php
            $profit_sharing_unit = '';
            switch($person->commission_type) {
                case 1:
                    $profit_sharing_unit = '$';
                    break;
                case 2:
                    $profit_sharing_unit = '%';
                    break;
            }
        @endphp
        <div class="commissionDiv">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('vending_profit_sharing', 'Profit Sharing ('.$profit_sharing_unit.')', ['class'=>'control-label']) !!}
                    {!! Form::text('vending_profit_sharing', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('vending_monthly_utilities', 'Monthly Utilities ($)', ['class'=>'control-label']) !!}
                {!! Form::text('vending_monthly_utilities', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        @if(!$person->is_dvm)
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('vending_clocker_adjustment', 'Clocker Adjustment (%)', ['class'=>'control-label']) !!}
                {!! Form::text('vending_clocker_adjustment', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">

                </div>
            </div>
        </div>
        @if(config('app.usage') != 'operator')
        <div class="row col-md-12 col-sm-12 col-xs-12">
            @if(!$person->vending)
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('serial_number', 'Serial Number', ['class'=>'control-label']) !!}
                    {!! Form::text('serial_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('terminal_id', 'Cashless Terminal ID', ['class'=>'control-label']) !!}
                    {!! Form::text('terminal_id', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('terminal_provider', 'Terminal Provider', ['class'=>'control-label']) !!}
                    {!! Form::select('terminal_provider',
                    [
                        '0'=>'None',
                        '1'=>'Nayax',
                        '2'=>'Castle',
                        '3'=>'XVend',
                        '4'=>'Auresys',
                        '5'=>'Beeptech'
                    ], null, ['class'=>'selectnotclear form-control', 'disabled'=>$disabled]) !!}
                </div>
            </div>
            @endif
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('cms_serial_number', 'CMS Serial Number', ['class'=>'control-label']) !!}
                    {!! Form::text('cms_serial_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        @endif
        @if(config('app.usage') != 'operator')
        <div class="row col-md-12 col-sm-12 col-xs-12">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('key_lock_number', 'Key Lock Number', ['class'=>'control-label']) !!}
                    {!! Form::text('key_lock_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('unit_number', '# of Unit', ['class'=>'control-label']) !!}
                    {!! Form::text('unit_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        @endif
        <div class="row col-md-12 col-sm-12 col-xs-12">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('is_pwp', 'Is Purchase w Purchase? (PWP)', ['class'=>'control-label']) !!}
                    {!! Form::select('is_pwp',
                    [
                        '0'=>'No',
                        '1'=>'Yes',
                    ], null, ['id'=>'is_pwp', 'class'=>'selectnotclear form-control', 'disabled'=>$disabled]) !!}
                </div>
            </div>
            <div class="isPwpDiv col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('pwp_adj_rate', 'PWP Adjustment Rate (%)', ['class'=>'control-label']) !!}
                    {!! Form::text('pwp_adj_rate', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('vending_id', 'Binded Vending', ['class'=>'control-label']) !!}
                <select name="vending_id" class="form-control select">
                    <option value="">Not Binding</option>
                    @foreach($vendings::whereNull('person_id')->orWhere('person_id', '=', 0)->orWhere('person_id', '=', $person->id)->latest()->get() as $vending)
                        <option value="{{$vending->id}}" {{isset($person->vending) && $person->vending->id == $vending->id ? 'selected' : ''}}>
                            {{$vending->serial_no}}
                            @if($vending->type)
                             - {{$vending->type}}
                            @endif
                            @if($vending->simcard)
                             ({{$vending->simcard->telco_name}} - {{$vending->simcard->simcard_no}})
                            @endif
                            @if($vending->cashlessTerminal)
                             ({{$vending->cashlessTerminal->provider_name}} - {{$vending->cashlessTerminal->terminal_id}})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        </div>
    </div>
</div>