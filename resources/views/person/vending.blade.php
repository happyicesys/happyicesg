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

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('commission_type', 'Commission Type', ['class'=>'control-label']) !!}
                {!! Form::select('commission_type', ['1'=>'Absolute Amount', '2'=>'Percentage'], null, ['class'=>'selectnotclear form-control', 'disabled'=>$disabled]) !!}
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('commission_package', 'Commission Package', ['class'=>'control-label']) !!}
                {!! Form::select('commission_package', ['1'=>'Both Utility & Comm', '2'=>'Whichever One is Higher'], null, ['class'=>'selectnotclear form-control', 'disabled'=>$disabled]) !!}
            </div>
        </div>
        @if(!$person->is_dvm)
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('vending_piece_price', 'Price Per Piece ($)', ['class'=>'control-label']) !!}
                {!! Form::text('vending_piece_price', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        @endif
        <div class="col-md-4 col-sm-4 col-xs-12">
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
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('vending_profit_sharing', 'Profit Sharing ('.$profit_sharing_unit.')', ['class'=>'control-label']) !!}
                {!! Form::text('vending_profit_sharing', null, ['class'=>'form-control']) !!}
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
        <div class="row col-md-12 col-sm-12 col-xs-12">
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
                        ''=>'',
                        '1'=>'Nayax',
                        '2'=>'Castle',
                        '3'=>'XVend',
                        '4'=>'Auresys',
                        '5'=>'Beeptech'
                    ], null, ['class'=>'select form-control', 'disabled'=>$disabled]) !!}
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('cms_serial_number', 'CMS Serial Number', ['class'=>'control-label']) !!}
                    {!! Form::text('cms_serial_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="row col-md-12 col-sm-12 col-xs-12">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    {!! Form::label('key_lock_number', 'Key Lock Number', ['class'=>'control-label']) !!}
                    {!! Form::text('key_lock_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        @if($person->vending)
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('serial_no', 'Binded Serial', ['class'=>'control-label']) !!}
                <a href="/vm/{{$person->vending->id}}/edit">
                    {!! Form::text('serial_no', $person->vending->serial_no, ['class'=>'form-control', 'readonely'=>'readonly']) !!}
                </a>
            </div>
        </div>
        @else
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('serial_no', 'Binded Serial', ['class'=>'control-label']) !!}
                {!! Form::text('serial_no', 'Not yet bind to machine', ['class'=>'form-control', 'readonly'=>'readonly']) !!}
            </div>
        </div>
        @endif
    </div>
</div>